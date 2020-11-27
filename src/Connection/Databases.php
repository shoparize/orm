<?php

namespace Benzine\ORM\Connection;

use Benzine\Exceptions\BenzineException;
use Benzine\Services\ConfigurationService;
use Monolog\Logger;

class Databases
{
    protected ConfigurationService $configurationService;
    protected Logger $logger;
    /** @var Database[] */
    protected static array $databases = [];

    public function __construct(
        ConfigurationService $configurationService,
        Logger $logger
    ) {
        $this->configurationService = $configurationService;
        $this->logger = $logger;

        foreach ($this->configurationService->get('databases') as $name => $config) {
            if (!isset(self::$databases[$name])) {
                $database = new Database($name, $config);
                if ('mysql' == $database->getType()) {
                    $database->onConnect(function (Database $database): void {
                        $database->getAdapter()
                            ->query('set global innodb_stats_on_metadata=0;')
                        ;
                    });
                }
                self::$databases[$name] = $database;
            }
        }
    }

    public function getDatabase(string $name): Database
    {
        if (!isset(self::$databases[$name])) {
            throw new BenzineException("No database configured called \"{$name}\".");
        }

        return self::$databases[$name];
    }

    /**
     * @return Database[]
     */
    public function getAll(): array
    {
        return self::$databases;
    }
}
