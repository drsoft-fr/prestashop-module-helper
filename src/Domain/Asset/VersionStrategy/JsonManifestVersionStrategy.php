<?php

namespace DrSoftFr\PrestaShopModuleHelper\Domain\Asset\VersionStrategy;

/**
 * Reads the versioned path of an asset from a JSON manifest file.
 */
class JsonManifestVersionStrategy implements VersionStrategyInterface
{
    private $manifestPath;

    private $manifestData;

    /**
     * @param string $manifestPath Absolute path to the manifest file
     */
    public function __construct(string $manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    /**
     * With a manifest, we don't really know or care about what
     * the version is. Instead, this returns the path to the
     * versioned file.
     *
     * {@inheritdoc}
     *
     */
    public function getVersion(string $path)
    {
        return $this->applyVersion($path);
    }

    /**
     * {@inheritdoc}
     */
    public function applyVersion(string $path)
    {
        return $this->getManifestPath($path) ?: $path;
    }

    /**
     * @param string $path Relative path to the asset in the manifest
     *
     * @return mixed|null The value associated with the given path in the manifest data, or null if not found
     */
    private function getManifestPath(string $path)
    {
        if (null === $this->manifestData) {
            if (!file_exists($this->manifestPath)) {
                throw new \RuntimeException(
                    sprintf(
                        'Asset manifest file "%s" does not exist. Did you forget to build the assets with npm or yarn?',
                        $this->manifestPath
                    )
                );
            }

            $this->manifestData = json_decode(file_get_contents($this->manifestPath), true);

            if (0 < json_last_error()) {
                throw new \RuntimeException(
                    sprintf(
                        'Error parsing JSON from asset manifest file "%s": ',
                        $this->manifestPath
                    ) . json_last_error_msg()
                );
            }
        }

        return $this->manifestData[$path] ?? null;
    }
}
