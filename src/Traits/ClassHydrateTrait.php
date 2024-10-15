<?php

namespace DrSoftFr\PrestaShopModuleHelper\Traits;

use InvalidArgumentException;

trait ClassHydrateTrait
{
    /**
     * Converts a string to camel case.
     *
     * @param string $str The string to convert.
     *
     * @return string The converted string.
     *
     * @throws InvalidArgumentException If the string is empty.
     */
    private function toCamelCase(string $str): string
    {
        if (empty($str)) {
            throw new InvalidArgumentException('String is empty');
        }

        $str = trim($str);
        $str = strtolower($str);
        $str = str_replace(
            [
                '/',
                '.',
                '+',
                '-',
                '_'
            ],
            ' ',
            $str
        );
        $str = ucwords($str);
        $str = str_replace(
            ' ',
            '',
            $str
        );

        return lcfirst($str);
    }

    /**
     * Hydrates the object with data.
     *
     * @param array $data The data to hydrate the object with.
     *
     * @return void
     *
     * @throws InvalidArgumentException Thrown if the method does not exist.
     */
    final public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($this->toCamelCase($key));

            if (!method_exists($this, $method)) {
                throw new InvalidArgumentException("Method $method does not exist.");
            }

            $this->$method($value);
        }
    }
}
