<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Tests\Mapping\Loader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Iban;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type as TypeConstraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\PropertyInfoLoader;
use Symfony\Component\Validator\Tests\Fixtures\Entity;
use Symfony\Component\Validator\Tests\Fixtures\PropertyInfoLoaderEntity;
use Symfony\Component\Validator\Validation;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class PropertyInfoLoaderTest extends TestCase
{
    public function testLoadClassMetadata()
    {
        $propertyInfoStub = $this->createMock(PropertyInfoExtractorInterface::class);
        $propertyInfoStub
            ->method('getProperties')
            ->willReturn([
                'nullableString',
                'string',
                'scalar',
                'object',
                'collection',
                'alreadyMappedType',
                'alreadyMappedNotNull',
                'alreadyMappedNotBlank',
                'alreadyPartiallyMappedCollection',
                'readOnly',
                'nonExistentField',
            ])
        ;
        $propertyInfoStub
            ->method('getTypes')
            ->will($this->onConsecutiveCalls(
                [new Type(Type::BUILTIN_TYPE_STRING, true)],
                [new Type(Type::BUILTIN_TYPE_STRING)],
                [new Type(Type::BUILTIN_TYPE_STRING, true), new Type(Type::BUILTIN_TYPE_INT), new Type(Type::BUILTIN_TYPE_BOOL)],
                [new Type(Type::BUILTIN_TYPE_OBJECT, true, Entity::class)],
                [new Type(Type::BUILTIN_TYPE_ARRAY, true, null, true, null, new Type(Type::BUILTIN_TYPE_OBJECT, false, Entity::class))],
                [new Type(Type::BUILTIN_TYPE_FLOAT, true)], // The existing constraint is float
                [new Type(Type::BUILTIN_TYPE_STRING, true)],
                [new Type(Type::BUILTIN_TYPE_STRING, true)],
                [new Type(Type::BUILTIN_TYPE_ARRAY, true, null, true, null, new Type(Type::BUILTIN_TYPE_FLOAT))],
                [new Type(Type::BUILTIN_TYPE_STRING)]
            ))
        ;
        $propertyInfoStub
            ->method('isWritable')
            ->will($this->onConsecutiveCalls(
                true,
                true,
                true,
                true,
                true,
                true,
                true,
                true,
                true,
                false
            ))
        ;

        $propertyInfoLoader = new PropertyInfoLoader($propertyInfoStub, $propertyInfoStub, $propertyInfoStub);

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addLoader($propertyInfoLoader)
            ->getValidator()
        ;

        $classMetadata = $validator->getMetadataFor(new PropertyInfoLoaderEntity());

        $nullableStringMetadata = $classMetadata->getPropertyMetadata('nullableString');
        $this->assertCount(1, $nullableStringMetadata);
        $nullableStringConstraints = $nullableStringMetadata[0]->getConstraints();
        $this->assertCount(1, $nullableStringConstraints);
        $this->assertInstanceOf(TypeConstraint::class, $nullableStringConstraints[0]);
        $this->assertSame('string', $nullableStringConstraints[0]->type);

        $stringMetadata = $classMetadata->getPropertyMetadata('string');
        $this->assertCount(1, $stringMetadata);
        $stringConstraints = $stringMetadata[0]->getConstraints();
        $this->assertCount(2, $stringConstraints);
        $this->assertInstanceOf(TypeConstraint::class, $stringConstraints[0]);
        $this->assertSame('string', $stringConstraints[0]->type);
        $this->assertInstanceOf(NotNull::class, $stringConstraints[1]);

        $scalarMetadata = $classMetadata->getPropertyMetadata('scalar');
        $this->assertCount(1, $scalarMetadata);
        $scalarConstraints = $scalarMetadata[0]->getConstraints();
        $this->assertCount(1, $scalarConstraints);
        $this->assertInstanceOf(TypeConstraint::class, $scalarConstraints[0]);
        $this->assertSame('scalar', $scalarConstraints[0]->type);

        $objectMetadata = $classMetadata->getPropertyMetadata('object');
        $this->assertCount(1, $objectMetadata);
        $objectConstraints = $objectMetadata[0]->getConstraints();
        $this->assertCount(1, $objectConstraints);
        $this->assertInstanceOf(TypeConstraint::class, $objectConstraints[0]);
        $this->assertSame(Entity::class, $objectConstraints[0]->type);

        $collectionMetadata = $classMetadata->getPropertyMetadata('collection');
        $this->assertCount(1, $collectionMetadata);
        $collectionConstraints = $collectionMetadata[0]->getConstraints();
        $this->assertCount(2, $collectionConstraints);
        $this->assertInstanceOf(All::class, $collectionConstraints[0]);
        $this->assertInstanceOf(NotNull::class, $collectionConstraints[0]->constraints[0]);
        $this->assertInstanceOf(TypeConstraint::class, $collectionConstraints[0]->constraints[1]);
        $this->assertSame(Entity::class, $collectionConstraints[0]->constraints[1]->type);

        $alreadyMappedTypeMetadata = $classMetadata->getPropertyMetadata('alreadyMappedType');
        $this->assertCount(1, $alreadyMappedTypeMetadata);
        $alreadyMappedTypeConstraints = $alreadyMappedTypeMetadata[0]->getConstraints();
        $this->assertCount(1, $alreadyMappedTypeMetadata);
        $this->assertInstanceOf(TypeConstraint::class, $alreadyMappedTypeConstraints[0]);

        $alreadyMappedNotNullMetadata = $classMetadata->getPropertyMetadata('alreadyMappedNotNull');
        $this->assertCount(1, $alreadyMappedNotNullMetadata);
        $alreadyMappedNotNullConstraints = $alreadyMappedNotNullMetadata[0]->getConstraints();
        $this->assertCount(1, $alreadyMappedNotNullMetadata);
        $this->assertInstanceOf(NotNull::class, $alreadyMappedNotNullConstraints[0]);

        $alreadyMappedNotBlankMetadata = $classMetadata->getPropertyMetadata('alreadyMappedNotBlank');
        $this->assertCount(1, $alreadyMappedNotBlankMetadata);
        $alreadyMappedNotBlankConstraints = $alreadyMappedNotBlankMetadata[0]->getConstraints();
        $this->assertCount(1, $alreadyMappedNotBlankMetadata);
        $this->assertInstanceOf(NotBlank::class, $alreadyMappedNotBlankConstraints[0]);

        $alreadyPartiallyMappedCollectionMetadata = $classMetadata->getPropertyMetadata('alreadyPartiallyMappedCollection');
        $this->assertCount(1, $alreadyPartiallyMappedCollectionMetadata);
        $alreadyPartiallyMappedCollectionConstraints = $alreadyPartiallyMappedCollectionMetadata[0]->getConstraints();
        $this->assertCount(2, $alreadyPartiallyMappedCollectionConstraints);
        $this->assertInstanceOf(All::class, $alreadyPartiallyMappedCollectionConstraints[0]);
        $this->assertInstanceOf(TypeConstraint::class, $alreadyPartiallyMappedCollectionConstraints[0]->constraints[0]);
        $this->assertSame('string', $alreadyPartiallyMappedCollectionConstraints[0]->constraints[0]->type);
        $this->assertInstanceOf(Iban::class, $alreadyPartiallyMappedCollectionConstraints[0]->constraints[1]);
        $this->assertInstanceOf(NotNull::class, $alreadyPartiallyMappedCollectionConstraints[0]->constraints[2]);

        $readOnlyMetadata = $classMetadata->getPropertyMetadata('readOnly');
        $this->assertEmpty($readOnlyMetadata);
    }

    /**
     * @dataProvider regexpProvider
     */
    public function testClassValidator(bool $expected, string $classValidatorRegexp = null)
    {
        $propertyInfoStub = $this->createMock(PropertyInfoExtractorInterface::class);
        $propertyInfoStub
            ->method('getProperties')
            ->willReturn(['string'])
        ;
        $propertyInfoStub
            ->method('getTypes')
            ->willReturn([new Type(Type::BUILTIN_TYPE_STRING)])
        ;

        $propertyInfoLoader = new PropertyInfoLoader($propertyInfoStub, $propertyInfoStub, $propertyInfoStub, $classValidatorRegexp);

        $classMetadata = new ClassMetadata(PropertyInfoLoaderEntity::class);
        $this->assertSame($expected, $propertyInfoLoader->loadClassMetadata($classMetadata));
    }

    public function regexpProvider()
    {
        return [
            [true, null],
            [true, '{^'.preg_quote(PropertyInfoLoaderEntity::class).'$|^'.preg_quote(Entity::class).'$}'],
            [false, '{^'.preg_quote(Entity::class).'$}'],
        ];
    }
}
