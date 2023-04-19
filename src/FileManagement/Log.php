<?php

namespace ShopwareCheckTool\FileManagement;

use RuntimeException;

class Log
{
    public function newGeneralLine(string $line = ''): void
    {
        $dir = __DIR__ . "/../Logs";
        if (!file_exists($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        $file = "$dir/connector.log";
        if (file_exists($file) && filesize($file) > 1048576) {
            unlink($file);
        }
        file_put_contents($file, date('Y-m-d H:i:s') . ' ' . $line . PHP_EOL, FILE_APPEND);
    }
}