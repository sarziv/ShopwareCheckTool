<?php


namespace ShopwareCheckTool\Traits;


trait Timable
{
    private array $time = [];

    protected function start(string $name): void
    {
        $this->time[$name] = microtime(true);
    }

    protected function end(string $name): float
    {
        if (!isset($this->start[$name])) {
            return 0.0;
        }

        return microtime(true) - $this->time[$name];
    }

    public function __destruct()
    {
        $timers = [];
        foreach ($this->time as $name => $start) {
            $timers[$name] = microtime(true) - $start;
        }
        echo "Tasks completed. Time: " . gmdate('H:i:s', (round(array_sum($timers)))) . ' seconds' . PHP_EOL;
    }
}