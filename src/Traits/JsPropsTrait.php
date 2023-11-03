<?php

declare(strict_types=1);

namespace DrSoftFr\PrestaShopModuleHelper\Traits;

use Media;

trait JsPropsTrait
{
    /**
     * @var array[]
     */
    private $jsProps = ['props' => []];

    /**
     * @param string $url
     * @param ?string $name
     *
     * @return bool
     */
    protected function addJsAjaxUrl(string $url, string $name = null): bool
    {
        $jsAjaxUrl = [];

        if (
            isset($this->getJsProps()['props']) &&
            isset($this->getJsProps()['props']['jsAjaxUrl']) &&
            is_array($this->getJsProps()['props']['jsAjaxUrl']) &&
            !empty($this->getJsProps()['props']['jsAjaxUrl'])
        ) {
            $jsAjaxUrl = $this->getJsProps()['props']['jsAjaxUrl'];
        }

        if (empty($name)) {
            if (
                !isset($this->module->name) ||
                empty($this->module->name)
            ) {
                return false;
            }

            $name = $this->module->name;
        }

        $jsAjaxUrl[$name] = $url;

        $this
            ->setJsProps([
                'jsAjaxUrl',
                $jsAjaxUrl
            ]);

        return true;
    }

    /**
     * @param object|array $object
     * @param string|null $name
     *
     * @return bool
     */
    protected function addJsObject($object, string $name = null): bool
    {
        $jsObject = [];

        if (
            isset($this->getJsProps()['props']) &&
            isset($this->getJsProps()['props']['jsObject']) &&
            is_array($this->getJsProps()['props']['jsObject']) &&
            !empty($this->getJsProps()['props']['jsObject'])
        ) {
            $jsObject = $this->getJsProps()['props']['jsObject'];
        }

        if (
            empty($name) &&
            is_object($object) &&
            get_class($object)
        ) {
            $name = get_class($object);
        }

        if (empty($name)) {
            if (
                !isset($this->module->name) ||
                empty($this->module->name)
            ) {
                return false;
            }

            $name = $this->module->name;
        }

        $jsObject[$name] = $object;

        $this
            ->setJsProps([
                'jsObject',
                $jsObject
            ]);

        return true;
    }

    /**
     * @param array $text
     *
     * @return bool
     */
    protected function addJsText(array $text): bool
    {
        if (
            isset($this->getJsProps()['props']) &&
            isset($this->getJsProps()['props']['jsText']) &&
            is_array($this->getJsProps()['props']['jsText']) &&
            !empty($this->getJsProps()['props']['jsText'])
        ) {
            $jsText = array_merge($this->getJsProps()['props']['jsText'], $text);
        } else {
            $jsText = $text;
        }

        $this
            ->setJsProps([
                'jsText',
                $jsText
            ]);

        return true;
    }

    protected function createJsProps()
    {
        $jsDef = Media::getJsDef();

        if (isset($jsDef['props'])) {
            foreach ($jsDef['props'] as $k => $v) {
                $this->setJsProps([$k, $v]);
            }
        }

        unset($jsDef);
    }

    /**
     * @return array
     */
    public function getJsProps(): array
    {
        return $this->jsProps;
    }

    /**
     * @param array $jsProps
     *
     * @return JsPropsTrait
     */
    public function setJsProps(array $jsProps)
    {
        if (!empty($jsProps) && isset($jsProps[0]) && isset($jsProps[1])) {
            $this->jsProps['props'][$jsProps[0]] = $jsProps[1];
        }

        return $this;
    }
}
