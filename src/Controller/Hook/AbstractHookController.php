<?php

declare(strict_types=1);

namespace DrSoftFr\PrestaShopModuleHelper\Controller\Hook;

use Context;
use Exception;
use Module;
use PrestaShop\PrestaShop\Adapter\LegacyLogger;

/**
 * Provides reusable methods for hook controllers.
 */
abstract class AbstractHookController
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var LegacyLogger
     */
    protected $logger;

    /**
     * @var Module
     */
    protected $module;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $props;

    /**
     * @param Module $module
     * @param string $file
     * @param string $path
     * @param array $props
     */
    public function __construct(
        Module $module,
        string $file,
        string $path,
        array  $props
    )
    {
        $this->module = $module;
        $this->file = $file;
        $this->path = $path;
        $this->props = $props;

        $this->logger = new LegacyLogger();
    }

    /**
     * @return Context
     *
     * @throws Exception
     */
    public function getContext(): Context
    {
        return $this->module->get('prestashop.adapter.legacy.context')->getContext();
    }
}
