<?php
declare(strict_types=1);

namespace Taoran\HyperfPackage\Helpers;

/**
 * 删除目录
 */
if (!function_exists('rm_dir')) {
    function rm_dir($path)
    {
        if (!is_dir($path)) {
            return false;
        }
        $dirs = scandir($path);
        foreach ($dirs as $dir) {
            if ($dir == '.' || $dir == '..') {
                continue;
            }
            if (is_dir($path . $dir)) {
                delete_dir($path . $dir);
            } else {
                @unlink($path . $dir);
            }
        }
        @rmdir($path);
    }
}
