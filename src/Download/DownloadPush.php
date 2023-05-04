<?php

namespace ShopwareCheckTool\Download;

class DownloadPush extends Download
{
    public function pushFile(string $name, string $json): array
    {
        try {
            return $this->save($name, $json);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'name' => $name,
                'message' => $e->getMessage()
            ];
        }
    }
}