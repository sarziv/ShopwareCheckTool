<?php


namespace ShopwareCheckTool\Traits;


use ShopwareCheckTool\FileManagement\FileLogger;

trait Timable
{
    private array $time = [];

    protected function start(string $name): void
    {
        $this->time[$name] = time();
    }

    protected function end(string $name): float
    {
        if (!isset($this->time[$name])) {
            return 0;
        }

        return time() - $this->time[$name];
    }

    public function __destruct()
    {
        $timers = [];
        $logger = new FileLogger();
        foreach ($this->time as $name => $start) {
            $timers[$name] = time() - $start;
            $logger->newGeneralFileLine( "Tasks: {$name}, Time: " . gmdate('H:i:s', $timers[$name]) . ' seconds');
        }
        $logger->newGeneralFileLine("Tasks completed. Time: " . gmdate('H:i:s', array_sum($timers)) . ' seconds');
    }
}