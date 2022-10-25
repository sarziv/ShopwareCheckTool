<?php


namespace ShopwareCheckTool\Traits;


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
        foreach ($this->time as $name => $start) {
            $timers[$name] = time() - $start;
        }
        echo "Tasks completed. Time: " . gmdate('H:i:s', array_sum($timers)) . ' seconds' . PHP_EOL;
    }
}