<?php

namespace ShopwareCheckTool\Download;

class DownloadPush extends Download
{
    public function pushFile(string $name, string $json): void
    {
        $this->save($name, $json);
    }
}