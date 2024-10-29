<?php

namespace DrSoftFr\PrestaShopModuleHelper\Domain\Asset;

/**
 * Asset package interface.
 */
interface PackageInterface
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
     * Returns an absolute or root-relative public path.
     *
     * @param string $path A path
     *
     * @return mixed|string The public path
     */
    public function getUrl(string $path);
}
