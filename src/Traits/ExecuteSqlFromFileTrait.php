<?php

declare(strict_types=1);

namespace DrSoftFr\PrestaShopModuleHelper\Traits;

use Db;
use Tools;

trait ExecuteSqlFromFileTrait
{
    /**
     * A helper that executes multiple database queries.
     *
     * @param string $filepath
     *
     * @return bool
     */
    public function executeSqlFromFile(string $filepath): bool
    {
        if (!file_exists($filepath)) {
            return true;
        }

        if (!$c = Tools::file_get_contents($filepath)) {
            return false;
        }

        $c = str_replace(['_DB_PREFIX_', '_MYSQL_ENGINE_'], [_DB_PREFIX_, _MYSQL_ENGINE_], $c);
        $a = preg_split("/;\s*[\r\n]+/", trim($c));
        $r = true;

        foreach ($a as $v) {
            if (!empty($v)) {
                $r &= Db::getInstance()->execute((trim($v)));
            }
        }

        unset($a, $c, $d, $v);

        return (bool)$r;
    }
}
