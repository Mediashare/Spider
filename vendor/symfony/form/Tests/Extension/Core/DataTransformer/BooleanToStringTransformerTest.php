<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Tests\Extension\Core\DataTransformer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer;

class BooleanToStringTransformerTest extends TestCase
{
    const TRUE_VALUE = '1';

    /**
     * @var BooleanToStringTransformer
     */
    protected $transformer;

    protected function setUp(): void
    {
        $this->transformer = new BooleanToStringTransformer(self::TRUE_VALUE);
    }

    protected function tearDown(): void
    {
        $this->transformer = null;
    }

    public function testTransform()
    {
        $this->assertEquals(self::TRUE_VALUE, $this->transformer->transform(true));
        $this->assertNull($this->transformer->transform(false));
    }

    // https://github.com/symfony/symfony/issues/8989
    public function testTransformAcceptsNull()
    {
        $this->assertNull($this->transformer->transform(null));
    }

    public function testTransformFailsIfString()
    {
        $this->expectException('Symfony\Component\Form\Exception\TransformationFailedException');
        $this->transformer->transform('1');
    }

    public function testReverseTransformFailsIfInteger()
    {
        $this->expectException('Symfony\Component\Form\Exception\TransformationFailedException');
        $this->transformer->reverseTransform(1);
    }

    public function testReverseTransform()
    {
        $this->assertTrue($this->transformer->reverseTransform(self::TRUE_VALUE));
        $this->assertTrue($this->transformer->reverseTransform('foobar'));
        $this->assertTrue($this->transformer->reverseTransform(''));
        $this->assertFalse($this->transformer->reverseTransform(null));
    }

    public function testCustomFalseValues()
    {
        $customFalseTransformer = new BooleanToStringTransformer(self::TRUE_VALUE, ['0', 'myFalse', true]);
        $this->assertFalse($customFalseTransformer->reverseTransform('myFalse'));
        $this->assertFalse($customFalseTransformer->reverseTransform('0'));
        $this->assertFalse($customFalseTransformer->reverseTransform(true));
    }

    public function testTrueValueContainedInFalseValues()
    {
        $this->expectException('Symfony\Component\Form\Exception\InvalidArgumentException');
        new BooleanToStringTransformer('0', [null, '0']);
    }

    public function testBeStrictOnTrueInFalseValueCheck()
    {
        $transformer = new BooleanToStringTransformer('0', [null, false]);
        $this->assertInstanceOf(BooleanToStringTransformer::class, $transformer);
    }
}
