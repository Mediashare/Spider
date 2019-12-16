<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Doctrine\Tests\Form\ChoiceList;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Form\ChoiceList\DoctrineChoiceLoader;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader;
use Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class DoctrineChoiceLoaderTest extends TestCase
{
    /**
     * @var ChoiceListFactoryInterface|MockObject
     */
    private $factory;

    /**
     * @var ObjectManager|MockObject
     */
    private $om;

    /**
     * @var ObjectRepository|MockObject
     */
    private $repository;

    /**
     * @var string
     */
    private $class;

    /**
     * @var IdReader|MockObject
     */
    private $idReader;

    /**
     * @var EntityLoaderInterface|MockObject
     */
    private $objectLoader;

    /**
     * @var \stdClass
     */
    private $obj1;

    /**
     * @var \stdClass
     */
    private $obj2;

    /**
     * @var \stdClass
     */
    private $obj3;

    protected function setUp(): void
    {
        $this->factory = $this->getMockBuilder('Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface')->getMock();
        $this->om = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $this->repository = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')->getMock();
        $this->class = 'stdClass';
        $this->idReader = $this->getMockBuilder('Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->idReader->expects($this->any())
            ->method('isSingleId')
            ->willReturn(true)
        ;

        $this->objectLoader = $this->getMockBuilder('Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface')->getMock();
        $this->obj1 = (object) ['name' => 'A'];
        $this->obj2 = (object) ['name' => 'B'];
        $this->obj3 = (object) ['name' => 'C'];

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->class)
            ->willReturn($this->repository);

        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->class)
            ->willReturn(new ClassMetadata($this->class));
    }

    public function testLoadChoiceList()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader
        );

        $choices = [$this->obj1, $this->obj2, $this->obj3];
        $value = function () {};
        $choiceList = new ArrayChoiceList($choices, $value);

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($choices);

        $this->assertEquals($choiceList, $loader->loadChoiceList($value));

        // no further loads on subsequent calls

        $this->assertEquals($choiceList, $loader->loadChoiceList($value));
    }

    public function testLoadChoiceListUsesObjectLoaderIfAvailable()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader,
            $this->objectLoader
        );

        $choices = [$this->obj1, $this->obj2, $this->obj3];
        $choiceList = new ArrayChoiceList($choices);

        $this->repository->expects($this->never())
            ->method('findAll');

        $this->objectLoader->expects($this->once())
            ->method('getEntities')
            ->willReturn($choices);

        $this->assertEquals($choiceList, $loaded = $loader->loadChoiceList());

        // no further loads on subsequent calls

        $this->assertSame($loaded, $loader->loadChoiceList());
    }

    public function testLoadValuesForChoices()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            null
        );

        $choices = [$this->obj1, $this->obj2, $this->obj3];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($choices);

        $this->assertSame(['1', '2'], $loader->loadValuesForChoices([$this->obj2, $this->obj3]));

        // no further loads on subsequent calls

        $this->assertSame(['1', '2'], $loader->loadValuesForChoices([$this->obj2, $this->obj3]));
    }

    public function testLoadValuesForChoicesDoesNotLoadIfEmptyChoices()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader
        );

        $this->repository->expects($this->never())
            ->method('findAll');

        $this->assertSame([], $loader->loadValuesForChoices([]));
    }

    public function testLoadValuesForChoicesDoesNotLoadIfSingleIntId()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader
        );

        $this->repository->expects($this->never())
            ->method('findAll');

        $this->idReader->expects($this->any())
            ->method('getIdValue')
            ->with($this->obj2)
            ->willReturn('2');

        $this->assertSame(['2'], $loader->loadValuesForChoices([$this->obj2]));
    }

    public function testLoadValuesForChoicesLoadsIfSingleIntIdAndValueGiven()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader
        );

        $choices = [$this->obj1, $this->obj2, $this->obj3];
        $value = function (\stdClass $object) { return $object->name; };

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($choices);

        $this->assertSame(['B'], $loader->loadValuesForChoices(
            [$this->obj2],
            $value
        ));
    }

    public function testLoadValuesForChoicesDoesNotLoadIfValueIsIdReader()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader
        );

        $value = [$this->idReader, 'getIdValue'];

        $this->repository->expects($this->never())
            ->method('findAll');

        $this->idReader->expects($this->any())
            ->method('getIdValue')
            ->with($this->obj2)
            ->willReturn('2');

        $this->assertSame(['2'], $loader->loadValuesForChoices(
            [$this->obj2],
            $value
        ));
    }

    public function testLoadChoicesForValues()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader
        );

        $choices = [$this->obj1, $this->obj2, $this->obj3];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($choices);

        $this->assertSame([$this->obj2, $this->obj3], $loader->loadChoicesForValues(['1', '2']));

        // no further loads on subsequent calls

        $this->assertSame([$this->obj2, $this->obj3], $loader->loadChoicesForValues(['1', '2']));
    }

    public function testLoadChoicesForValuesDoesNotLoadIfEmptyValues()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader
        );

        $this->repository->expects($this->never())
            ->method('findAll');

        $this->assertSame([], $loader->loadChoicesForValues([]));
    }

    public function testLoadChoicesForValuesLoadsOnlyChoicesIfSingleIntId()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader,
            $this->objectLoader
        );

        $choices = [$this->obj2, $this->obj3];

        $this->idReader->expects($this->any())
            ->method('getIdField')
            ->willReturn('idField');

        $this->repository->expects($this->never())
            ->method('findAll');

        $this->objectLoader->expects($this->once())
            ->method('getEntitiesByIds')
            ->with('idField', [4 => '3', 7 => '2'])
            ->willReturn($choices);

        $this->idReader->expects($this->any())
            ->method('getIdValue')
            ->willReturnMap([
                [$this->obj2, '2'],
                [$this->obj3, '3'],
            ]);

        $this->assertSame(
            [4 => $this->obj3, 7 => $this->obj2],
            $loader->loadChoicesForValues([4 => '3', 7 => '2']
        ));
    }

    public function testLoadChoicesForValuesLoadsAllIfSingleIntIdAndValueGiven()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader
        );

        $choices = [$this->obj1, $this->obj2, $this->obj3];
        $value = function (\stdClass $object) { return $object->name; };

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($choices);

        $this->assertSame([$this->obj2], $loader->loadChoicesForValues(
            ['B'],
            $value
        ));
    }

    public function testLoadChoicesForValuesLoadsOnlyChoicesIfValueIsIdReader()
    {
        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $this->idReader,
            $this->objectLoader
        );

        $choices = [$this->obj2, $this->obj3];
        $value = [$this->idReader, 'getIdValue'];

        $this->idReader->expects($this->any())
            ->method('getIdField')
            ->willReturn('idField');

        $this->repository->expects($this->never())
            ->method('findAll');

        $this->objectLoader->expects($this->once())
            ->method('getEntitiesByIds')
            ->with('idField', ['2'])
            ->willReturn($choices);

        $this->idReader->expects($this->any())
            ->method('getIdValue')
            ->willReturnMap([
                [$this->obj2, '2'],
                [$this->obj3, '3'],
            ]);

        $this->assertSame([$this->obj2], $loader->loadChoicesForValues(['2'], $value));
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation Not explicitly passing an instance of "Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader" to "Symfony\Bridge\Doctrine\Form\ChoiceList\DoctrineChoiceLoader" when it can optimize single id entity "%s" has been deprecated in 4.3 and will not apply any optimization in 5.0.
     */
    public function testLoaderWithoutIdReaderCanBeOptimized()
    {
        $obj1 = new SingleIntIdEntity('1', 'one');
        $obj2 = new SingleIntIdEntity('2', 'two');

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->expects($this->once())
            ->method('getIdentifierFieldNames')
            ->willReturn(['idField'])
        ;
        $metadata->expects($this->any())
            ->method('getIdentifierValues')
            ->willReturnCallback(function ($obj) use ($obj1, $obj2) {
                if ($obj === $obj1) {
                    return ['idField' => '1'];
                }
                if ($obj === $obj2) {
                    return ['idField' => '2'];
                }

                return null;
            })
        ;

        $this->om = $this->createMock(ObjectManager::class);
        $this->om->expects($this->once())
            ->method('getClassMetadata')
            ->with(SingleIntIdEntity::class)
            ->willReturn($metadata)
        ;
        $this->om->expects($this->any())
            ->method('contains')
            ->with($this->isInstanceOf(SingleIntIdEntity::class))
            ->willReturn(true)
        ;

        $loader = new DoctrineChoiceLoader(
            $this->om,
            SingleIntIdEntity::class,
            null,
            $this->objectLoader
        );

        $choices = [$obj1, $obj2];

        $this->repository->expects($this->never())
            ->method('findAll');

        $this->objectLoader->expects($this->once())
            ->method('getEntitiesByIds')
            ->with('idField', ['1'])
            ->willReturn($choices);

        $this->assertSame([$obj1], $loader->loadChoicesForValues(['1']));
    }

    /**
     * @group legacy
     *
     * @deprecationMessage Passing an instance of "Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader" to "Symfony\Bridge\Doctrine\Form\ChoiceList\DoctrineChoiceLoader" with an entity class "stdClass" that has a composite id is deprecated since Symfony 4.3 and will throw an exception in 5.0.
     */
    public function testPassingIdReaderWithoutSingleIdEntity()
    {
        $idReader = $this->createMock(IdReader::class);
        $idReader->expects($this->once())
            ->method('isSingleId')
            ->willReturn(false)
        ;

        $loader = new DoctrineChoiceLoader(
            $this->om,
            $this->class,
            $idReader,
            $this->objectLoader
        );

        $this->assertInstanceOf(DoctrineChoiceLoader::class, $loader);
    }
}
