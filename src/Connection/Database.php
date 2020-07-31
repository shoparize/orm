<?php

namespace Benzine\ORM\Connection;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Metadata\Metadata;

class Database
{
    private string $name = 'default';
    private string $type = 'mysql';
    private string $hostname;
    private string $username;
    private string $password;
    private string $database;
    private string $charset = 'utf8mb4';

    private ?Adapter $adapter = null;

    public function __construct(string $name = null, array $config = null)
    {
        if ($name) {
            $this->setName($name);
        }
        if (isset($config['type'])) {
            $this->setType($config['type']);
        }
        if (isset($config['host'])) {
            $this->setHostname($config['host']);
        }
        if (isset($config['username'])) {
            $this->setUsername($config['username']);
        }
        if (isset($config['password'])) {
            $this->setPassword($config['password']);
        }
        if (isset($config['database'])) {
            $this->setDatabase($config['database']);
        }
        if (isset($config['charset'])) {
            $this->setCharset($config['charset']);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Database
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Database
    {
        $this->type = $type;

        return $this;
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): Database
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): Database
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): Database
    {
        $this->password = $password;

        return $this;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function setDatabase(string $database): Database
    {
        $this->database = $database;

        return $this;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function setCharset(string $charset): Database
    {
        $this->charset = $charset;

        return $this;
    }

    public function getAdapter(): Adapter
    {
        if (!$this->adapter) {
            $this->adapter = new Adapter($this->getArray());
        }

        return $this->adapter;
    }

    public function getMetadata(): Metadata
    {
        return new Metadata($this->getAdapter());
    }

    public function getArray(): array
    {
        return [
            'driver' => 'pdo',
            'pdodriver' => $this->getType(),
            'type' => $this->getType(),
            'charset' => $this->getCharset(),
            'host' => $this->getHostname(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'database' => $this->getDatabase(),
        ];
    }
}
