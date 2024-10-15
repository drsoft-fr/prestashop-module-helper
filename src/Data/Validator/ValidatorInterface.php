<?php

declare(strict_types=1);

namespace DrSoftFr\PrestaShopModuleHelper\Data\Validator;

use Exception;

/**
 * ValidatorInterface interface is responsible for defining a method to validate data.
 */
interface ValidatorInterface
{
    /**
     * Validates all the data fields.
     *
     * @param array $data The data array to validate.
     *
     * @return bool Returns true if all the fields pass the validation.
     *
     * @throws Exception If any of the data fields fail validation.
     */
    function validate(array $data): bool;
}
