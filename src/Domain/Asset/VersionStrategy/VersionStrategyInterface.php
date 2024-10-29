<?php

namespace DrSoftFr\PrestaShopModuleHelper\Domain\Asset\VersionStrategy;

/**
 * Asset version strategy interface.
 */
interface VersionStrategyInterface
{
    /**
     * Returns the asset version for an asset.
     *
     * @param string $path A path
     *
     * @return mixed|string The version string
     */
    public function getVersion(string $path);

    /**
     * Applies version to the supplied path.
     *
     * @param string $path A path
     *
     * @return mixed|string The versioned path
     */
    public function applyVersion(string $path);
}
