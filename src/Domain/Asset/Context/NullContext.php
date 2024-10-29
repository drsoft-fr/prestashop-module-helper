<?php

namespace DrSoftFr\PrestaShopModuleHelper\Domain\Asset\Context;

/**
 * A context that does nothing.
 */
class NullContext implements ContextInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBasePath()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function isSecure()
    {
        return false;
    }
}
