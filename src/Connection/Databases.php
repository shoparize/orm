<?php

namespace Benzine\ORM\Connection;

use Benzine\Exceptions\BenzineException;
use Benzine\ORM\Exception\Exception;
use Benzine\Services\ConfigurationService;

class Databases
{
    protected ConfigurationService $configurationService;
    /** @var Database[] */
    protected array $databases;

    private $index;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;

        foreach ($this->configurationService->get('databases') as $name => $config) {
            $database = new Database($name, $config);
            if ('mysql' == $database->getType()) {
                $database->getAdapter()
                    ->query('set global innodb_stats_on_metadata=0;')
                ;
            }
            $this->databases[$database->getName()] = $database;
        }
    }

    public function getDatabase(string $name): Database
    {
        if (!isset($this->databases[$name])) {
            throw new BenzineException("No database configured called \"{$name}\".");
        }

        return $this->databases[$name];
    }

    /**
     * @return Database[]
     */
    public function getAll(): array
    {
        return $this->databases;
    }
}
