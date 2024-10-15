<?php

declare(strict_types=1);

namespace DrSoftFr\PrestaShopModuleHelper\Data\Validator;

use Closure;
use Exception;

abstract class AbstractValidator
{
    /**
     * Gets the condition based on the provided function name.
     *
     * @param string $fn The function name.
     *
     * @return callable|string The condition function or the built-in PHP function associated with the given function name.
     *
     * @throws Exception If the function name does not exist.
     */
    private function getCondition(string $fn)
    {
        return $this->getFunction($fn, 'condition');
    }

    /**
     * Retrieves the exception code for a specific constant in an exception class.
     *
     * @param string $const The constant to generate the exception code from.
     * @param Exception $class The exception class.
     *
     * @return int The exception code for the specified constant, or 0 if the constant does not exist.
     */
    private function getExceptionCode(string $const, Exception $class): int
    {
        $exceptionCodeConst = 'INVALID_' . strtoupper($const);
        $fullyQualifiedConstantName = get_class($class) . '::' . $exceptionCodeConst;

        if (defined($fullyQualifiedConstantName)) {
            return (int)constant($fullyQualifiedConstantName);
        }

        return 0;
    }

    /**
     * Retrieves a function closure based on the provided function and property.
     *
     * @param 'isArray'|'isBool'|'isEmpty'|'isInt'|'isSet'|'isString' $fn The function name.
     * @param 'condition'|'message' $prop The property name.
     *
     * @return Closure|string The function closure or property string.
     *
     * @throws Exception If the function name does not exist.
     * @throws Exception If the property name does not exist.
     */
    private function getFunction(string $fn, string $prop)
    {
        $closures = [
            'empty' => function (string $field): string {
                return 'empty ' . $field . ' field';
            },
            'isset' => function (string $field): string {
                return $field . ' field is not set';
            },
            'type' => function (string $field): string {
                return 'invalid ' . $field . ' field';
            },
        ];

        $fns = [
            'isArray' => [
                'condition' => 'is_array',
                'message' => $closures['type']
            ],
            'isBool' => [
                'condition' => 'is_bool',
                'message' => $closures['type']
            ],
            'isEmpty' => [
                'condition' => function ($value): bool {
                    return !empty($value);
                },
                'message' => $closures['empty']
            ],
            'isInt' => [
                'condition' => 'is_int',
                'message' => $closures['type']
            ],
            'isSet' => [
                'condition' => function ($value): bool {
                    return isset($value);
                },
                'message' => $closures['isset']
            ],
            'isString' => [
                'condition' => 'is_string',
                'message' => $closures['type']
            ],
        ];

        if (!key_exists($fn, $fns)) {
            throw new Exception('The function "' . $fn . '" does not exist.');
        }

        if (!key_exists($prop, $fns[$fn])) {
            throw new Exception('The property "' . $prop . '" does not exist.');
        }

        return $fns[$fn][$prop];
    }

    /**
     * Gets the error message corresponding to the given function name.
     *
     * @param string $fn The function name.
     *
     * @return Closure The closure that returns the error message.
     *
     * @throws Exception If the function name does not exist.
     */
    private function getMessage(string $fn): Closure
    {
        return $this->getFunction($fn, 'message');
    }

    /**
     * Validates that the specified field in the data array is an array.
     *
     * @param array $data The data array to validate.
     * @param string $field The name of the field to validate.
     * @param Exception $class The exception class to throw if validation fails
     *
     * @throws Exception If the specified field is not an array.
     */
    protected function isArray(array $data, string $field, Exception $class): void
    {
        $this->testField('isArray', $data, $field, $class);
    }

    /**
     * Checks if a specific field in the data array is of type bool.
     *
     * @param array $data The data array to check.
     * @param string $field The field name to check in the data array.
     * @param Exception $class The exception class to throw if validation fails
     *
     * @throws Exception If the field is not of type bool.
     */
    protected function isBool(array $data, string $field, Exception $class): void
    {
        $this->testField('isBool', $data, $field, $class);
    }

    /**
     * Checks if a specific field in the data array is empty.
     *
     * @param array $data The data array to check.
     * @param string $field The field name to check in the data array.
     * @param Exception $class The exception class to throw if validation fails
     *
     * @throws Exception If the field is empty.
     */
    protected function isEmpty(array $data, string $field, Exception $class): void
    {
        $this->testField('isEmpty', $data, $field, $class);
    }

    /**
     * Validates that the specified field in the data array is an integer.
     *
     * @param array $data The data array to validate.
     * @param string $field The field to check for integer value.
     * @param Exception $class The exception class to throw if validation fails
     *
     * @throws Exception If the specified field is not an integer.
     */
    protected function isInt(array $data, string $field, Exception $class): void
    {
        $this->testField('isInt', $data, $field, $class);
    }

    /**
     * Check if a field is set in the data array
     *
     * @param array $data The data array
     * @param string $field The field to check
     * @param Exception $class The exception class to throw if validation fails
     *
     * @throws Exception If the field is not set in the data array
     */
    protected function isSet(array $data, string $field, Exception $class): void
    {
        $this->testField('isSet', $data, $field, $class);
    }

    /**
     * Validates that the specified field in the data array is a string.
     *
     * @param array $data The data array to validate.
     * @param string $field The field to validate as a string.
     * @param Exception $class The exception class to throw if validation fails
     *
     * @throws Exception If the specified field is not a string.
     */
    protected function isString(array $data, string $field, Exception $class): void
    {
        $this->testField('isString', $data, $field, $class);
    }

    /**
     * Validates a specific field in the data array using a given condition.
     * Throws an Exception if the condition is not met.
     *
     * @param string $fn The name of the condition function to use.
     * @param array $configuration The configuration array to validate.
     * @param string $field The field to validate in the configuration array.
     * @param Exception $class The exception class to throw if validation fails
     *
     * @throws Exception If the condition is not met.
     */
    private function testField(string $fn, array $configuration, string $field, Exception $class): void
    {
        if (!$this->getCondition($fn)($configuration[$field])) {
            throw new $class(
                $this->getMessage($fn)($field),
                $this->getExceptionCode($field, $class)
            );
        }
    }
}
