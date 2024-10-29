<?php

namespace DrSoftFr\PrestaShopModuleHelper\Domain\Asset\Context;

/**
 * Holds information about the current request.
 */
interface ContextInterface
{
    /**
     * Gets the base path.
     *
     * @return string The base path
     */
    public function getBasePath();

    /**
     * Checks whether the request is secure or not.
     *
     * @return bool true if the request is secure, false otherwise
     */
    public function isSecure();
}
