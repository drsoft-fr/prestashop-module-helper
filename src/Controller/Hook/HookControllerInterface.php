<?php

declare(strict_types=1);

namespace DrSoftFr\PrestaShopModuleHelper\Controller\Hook;

/**
 * Interface for hook controller.
 */
interface HookControllerInterface
{
    /**
     * @return mixed
     */
    public function run();
}
