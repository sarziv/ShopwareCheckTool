<?php


namespace ShopwareCheckTool\FileManagement;


use JsonException;
use ShopwareCheckTool\Requests\Shopware;

abstract class File
{
    protected string $name;
    protected Shopware $shopware;
    private const DOWNLOAD_FOLDER = '/../Logs/Downloaded/';
    protected const COMPLETED_FOLDER = '/../Logs/Completed/';
    private string $path = '';

    public function readFile(string $fileName, bool $withJSON = true): array
    {
        $array = [];
        $file = "{$this->getFolder()}$fileName" . ($withJSON ? '.json' : '');
        if (file_exists($file)) {
            try {
                $array = @json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR) ?? [];
            } catch (JsonException $exception) {
                $this->newGeneralFileLine("JsonException:{{$this->name} file,Message: {$exception->getMessage()}");
            }
        }
        if (!$array) {
            $this->newGeneralFileLine("{$this->name} file is empty. Task skipped.");
            return [];
        }
        $this->newGeneralFileLine("{$this->name} count: " . count($array));

        return $array;
    }

    public function clear(): void
    {
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name.json";
        $fileLog = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name-Log.json";
        if (file_exists($file)) {
            unlink($file);
        }
        if (file_exists($fileLog)) {
            unlink($fileLog);
        }
    }

    public function newGeneralFileLine(string $line = ''): void
    {
        $file = __DIR__ . "/../Logs/general.log";
        file_put_contents($file, date('Y-m-d H:i:s') . ' ' . $line . PHP_EOL, FILE_APPEND);
        if (filesize($file) > 1048576) {
            unlink($file);
        }
    }

    public function newFileLine(string $line = ''): void
    {
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name.log";
        file_put_contents($file, $line . PHP_EOL, FILE_APPEND);
    }

    public function newFileLineLog(string $line = ''): void
    {
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name-Log.json";
        file_put_contents($file, date('Y-m-d H:i:s') . ' ' . $line . PHP_EOL, FILE_APPEND);
    }

    public function getFiles(): array
    {
        return array_diff(scandir($this->path), array('..', '.'));
    }

    protected function getFolder(): string
    {
        return $this->path ?: (__DIR__ . self::DOWNLOAD_FOLDER);
    }

    protected function useCompletedFolder(): void
    {
        $this->path = __DIR__ . self::COMPLETED_FOLDER . "{$this->shopware->configuration->getPath()}/";
    }
}