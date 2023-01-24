<?php


namespace ShopwareCheckTool\FileManagement;


use ShopwareCheckTool\Requests\Shopware;

abstract class File
{
    protected string $name;
    protected Shopware $shopware;
    private const DOWNLOAD_FOLDER = '/../Logs/Downloaded/';
    private const COMPLETED_FOLDER = '/../Logs/Completed/';
    private string $path = '';

    public function readFile(string $fileName, bool $withJSON = true): array
    {
        $array = [];
        $file = "{$this->getFolder()}$fileName" . ($withJSON ? '.json' : '');
        if (file_exists($file)) {
            $array = @json_decode(file_get_contents($file), true) ?? [];
        }
        if (!@$array) {
            echo "{$this->name} file is empty. Task skipped." . PHP_EOL;
        }

        return $array;
    }

    public function saveFile(array $log = []): void
    {
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name.json";
        if (file_exists($file)) {
            unlink($file);
            echo "Generating new file." . PHP_EOL;
        }
        file_put_contents($file, json_encode($log, JSON_PRETTY_PRINT));
        echo "{$this->name} completed." . PHP_EOL;
    }

    public function getFiles(): array
    {
        return array_diff(scandir($this->path), array('..', '.'));
    }

    protected function getFolder(): string
    {
        return $this->path ?: (__DIR__ . self::DOWNLOAD_FOLDER);
    }

    protected function setFolder(string $filePath): void
    {
        $this->path = $filePath;
    }

    protected function useCompletedFolder(): void
    {
        $this->path = __DIR__ . self::COMPLETED_FOLDER . "{$this->shopware->configuration->getPath()}/";
    }
}