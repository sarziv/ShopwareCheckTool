<?php


namespace ShopwareCheckTool\FileManagement;


use Generator;
use JsonException;
use ShopwareCheckTool\Requests\Shopware;

abstract class File extends Log
{
    protected string $name;
    protected Shopware $shopware;
    private const DOWNLOAD_FOLDER = '/../Logs/Downloaded/';
    protected const COMPLETED_FOLDER = '/../Logs/Completed/';
    private string $path = '';
    private string $extension = '.json';

    public function readFile(string $fileName): array
    {
        $array = [];
        $file = "{$this->getFolder()}$fileName" . $this->extension;
        if (file_exists($file)) {
            try {
                $array = @json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR) ?? [];
            } catch (JsonException $exception) {
                $this->newGeneralLine("JsonException:{{$this->name} file,Message: {$exception->getMessage()}");
            }
        }
        if (!$array) {
            $this->newGeneralLine("{$this->name} file is empty. Task skipped.");
            return [];
        }
        $this->newGeneralLine("{$this->name} count: " . count($array));

        return $array;
    }

    public function readLogFile(string $fileName): ?string
    {
        $this->useCompletedFolder();
        $file = "{$this->getFolder()}$fileName" . $this->extension;
        if (!file_exists($file)) {
            return null;
        }
        return file_get_contents($file, true);
    }

    public function getInvalidFile(string $fileName): ?string
    {
        $this->useCompletedInvalidFolder();
        $file = "{$this->getFolder()}$fileName" . $this->extension;
        if (!file_exists($file)) {
            return null;
        }
        return file_get_contents($file, true);
    }

    /**
     * @param string $fileName
     * @return Generator|null
     */
    public function readInvalidFile(string $fileName): ?Generator
    {
        $file = "{$this->getFolder()}$fileName";
        if (!file_exists($file)) {
            return null;
        }
        if ($file = fopen($file, 'rwb', true)) {
            while (!feof($file)) {
                yield fgets($file);
            }
            fclose($file);
        }
        return null;
    }

    public function clear(): void
    {
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/Invalid/$this->name.log";
        $fileLog = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name.log";
        if (file_exists($file)) {
            unlink($file);
        }
        if (file_exists($fileLog)) {
            unlink($fileLog);
        }
    }

    public function newInvalidLine(string $line = ''): void
    {
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/Invalid/$this->name.log";
        file_put_contents($file, $line . PHP_EOL, FILE_APPEND);
    }

    public function newLogLine(string $line = ''): void
    {
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name.log";
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

    public function useCompletedFolder(): void
    {
        $this->extension = '.log';
        $this->path = __DIR__ . self::COMPLETED_FOLDER . "{$this->shopware->configuration->getPath()}/";
    }

    protected function useCompletedInvalidFolder(): void
    {
        $this->extension = '.log';
        $this->path = __DIR__ . self::COMPLETED_FOLDER . "{$this->shopware->configuration->getPath()}/Invalid/";
    }
}