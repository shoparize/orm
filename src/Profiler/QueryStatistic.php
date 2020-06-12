<?php

namespace Benzine\ORM\Profiler;

use Benzine\ORM\Interfaces\QueryStatisticInterface;

class QueryStatistic implements QueryStatisticInterface
{
    /** @var string */
    private $sql;
    /** @var float */
    private $time;
    /** @var array */
    private $callPoints;

    public function __toArray(): array
    {
        return [
            'Time' => number_format($this->getTime() * 1000, 3).'ms',
            'Query' => $this->getSql(),
        ];
    }

    public function getCallPoints(): array
    {
        return $this->callPoints;
    }

    public function setCallPoints(array $callPoints): QueryStatistic
    {
        $this->callPoints = $callPoints;

        return $this;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function setSql(string $sql): QueryStatistic
    {
        $this->sql = $sql;

        return $this;
    }

    public function getTime(): float
    {
        return $this->time;
    }

    public function setTime(float $time): QueryStatistic
    {
        $this->time = $time;

        return $this;
    }
}
