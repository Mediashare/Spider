<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Tests\Constraints;

use Symfony\Component\Validator\Constraints\AbstractComparison;
use Symfony\Component\Validator\Constraints\Positive;

/**
 * @author Jan Schädlich <jan.schaedlich@sensiolabs.de>
 */
class GreaterThanValidatorWithPositiveConstraintTest extends GreaterThanValidatorTest
{
    protected function createConstraint(array $options = null)
    {
        return new Positive();
    }

    /**
     * {@inheritdoc}
     */
    public function provideValidComparisons()
    {
        return [
            [2, 0],
            [2.5, 0],
            ['333', '0'],
            [null, 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function provideInvalidComparisons()
    {
        return [
            [0, '0', 0, '0', 'integer'],
            [-1, '-1', 0, '0', 'integer'],
            [-2, '-2', 0, '0', 'integer'],
            [-2.5, '-2.5', 0, '0', 'integer'],
        ];
    }

    public function testThrowsConstraintExceptionIfPropertyPath()
    {
        $this->expectException('Symfony\Component\Validator\Exception\ConstraintDefinitionException');
        $this->expectExceptionMessage('The "propertyPath" option of the "Symfony\Component\Validator\Constraints\Positive" constraint cannot be set.');

        return new Positive(['propertyPath' => 'field']);
    }

    public function testThrowsConstraintExceptionIfValue()
    {
        $this->expectException('Symfony\Component\Validator\Exception\ConstraintDefinitionException');
        $this->expectExceptionMessage('The "value" option of the "Symfony\Component\Validator\Constraints\Positive" constraint cannot be set.');

        return new Positive(['value' => 0]);
    }

    /**
     * @dataProvider provideInvalidConstraintOptions
     */
    public function testThrowsConstraintExceptionIfNoValueOrPropertyPath($options)
    {
        $this->expectException('Symfony\Component\Validator\Exception\ConstraintDefinitionException');
        $this->expectExceptionMessage('requires either the "value" or "propertyPath" option to be set.');
        $this->markTestSkipped('Value option always set for Positive constraint.');
    }

    public function testThrowsConstraintExceptionIfBothValueAndPropertyPath()
    {
        $this->expectException('Symfony\Component\Validator\Exception\ConstraintDefinitionException');
        $this->expectExceptionMessage('requires only one of the "value" or "propertyPath" options to be set, not both.');
        $this->markTestSkipped('Value option is set for Positive constraint automatically');
    }

    public function testNoViolationOnNullObjectWithPropertyPath()
    {
        $this->markTestSkipped('PropertyPath option is not used in Positive constraint');
    }

    public function testInvalidValuePath()
    {
        $this->markTestSkipped('PropertyPath option is not used in Positive constraint');
    }

    /**
     * @dataProvider provideValidComparisonsToPropertyPath
     */
    public function testValidComparisonToPropertyPath($comparedValue)
    {
        $this->markTestSkipped('PropertyPath option is not used in Positive constraint');
    }

    /**
     * @dataProvider throwsOnInvalidStringDatesProvider
     */
    public function testThrowsOnInvalidStringDates(AbstractComparison $constraint, $expectedMessage, $value)
    {
        $this->markTestSkipped('The compared value cannot be an invalid string date because it is hardcoded to 0.');
    }
}
