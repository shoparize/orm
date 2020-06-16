<?php

namespace Benzine\ORM\Connection;

use Benzine\ORM\Exception\Exception;
use Benzine\Services\ConfigurationService;

class Databases {
    protected ConfigurationService $configurationService;
    /** @var Database[] */
    protected array $databases;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;

        foreach($this->configurationService->get('databases') as $name => $config){
            $database = new Database($name, $config);
            if($database->getType() == 'mysql'){
                $database->getAdapter()
                    ->query('set global innodb_stats_on_metadata=0;');
            }
            $this->databases[$database->getName()] = $database;
        }
    }

    public function getDatabase(string $name) : Database {
        if(!isset($this->databases[$name])){
            throw new Exception("No database configured called \"{$name}\".");
        }
        return $this->databases[$name];
    }

}