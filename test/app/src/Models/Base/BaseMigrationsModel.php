<?php

namespace Benzine\ORM\Tests\Models\Base;

use Benzine\ORM\Tests\Models;
use Benzine\ORM\Tests\TableGateways;
use Benzine\ORM\Tests\Services;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Benzine\ORM\Abstracts\Model as AbstractModel;
use Benzine\ORM\Interfaces\ModelInterface as ModelInterface;
use Benzine\App as App;

/**            ___                         __
 *            / _ \___ ____  ___ ____ ____/ /
 *           / // / _ `/ _ \/ _ `/ -_) __/_/
 *          /____/\_,_/_//_/\_, /\__/_/ (_)
 *                         /___/.
 *
 * Anything in this file is prone to being overwritten!
 *
 * This file was programmatically generated. To modify
 * this classes behaviours, do so in the class that
 * extends this, or modify the Laminator Template!
 */
abstract class BaseMigrationsModel extends AbstractModel implements ModelInterface
{
    // Declare what fields are available on this object
    public const FIELD_VERSION = 'version';
    public const FIELD_MIGRATION_NAME = 'migration_name';
    public const FIELD_START_TIME = 'start_time';
    public const FIELD_END_TIME = 'end_time';
    public const FIELD_BREAKPOINT = 'breakpoint';

    public const TYPE_VERSION = 'bigint';
    public const TYPE_MIGRATION_NAME = 'varchar';
    public const TYPE_START_TIME = 'timestamp';
    public const TYPE_END_TIME = 'timestamp';
    public const TYPE_BREAKPOINT = 'tinyint';

    // Constant arrays defined by ENUMs

    // Constants defined by ENUMs

    protected array $_primary_keys = [
        'version' => 'version',
    ];

    // PHPType: int. DBType: bigint. Max Length: 9223372036854775807.
    protected ?int $version = null;

    // PHPType: string. DBType: varchar. Max Length: .
    protected ?string $migration_name = null;

    // PHPType: string. DBType: timestamp. Max Length: .
    protected ?string $start_time = null;

    // PHPType: string. DBType: timestamp. Max Length: .
    protected ?string $end_time = null;

    // PHPType: int. DBType: tinyint. Max Length: 127.
    protected ?int $breakpoint = null;



    /** Caching entities **/
    protected array $cache = [];



    /**
     * @param array $data an array of a Models\MigrationsModel's properties
     *
     * @return Models\MigrationsModel
     */
    public static function factory(array $data = [])
    {
        return parent::factory($data);
    }

    /**
     * Returns an array describing the properties of this model.
     *
     * @return array
     */
    public function getPropertyMeta(): array
    {
        return [
            self::FIELD_VERSION => [
                'type' => self::TYPE_VERSION,
            ],
            self::FIELD_MIGRATION_NAME => [
                'type' => self::TYPE_MIGRATION_NAME,
                'length' => 100,
            ],
            self::FIELD_START_TIME => [
                'type' => self::TYPE_START_TIME,
            ],
            self::FIELD_END_TIME => [
                'type' => self::TYPE_END_TIME,
            ],
            self::FIELD_BREAKPOINT => [
                'type' => self::TYPE_BREAKPOINT,
            ],
        ];
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return self
     */
    public function setVersion(int $version = null): self
    {
        $this->version = $version;

        return $this;
    }

    public function getMigration_name(): ?string
    {
        return $this->migration_name;
    }

    /**
     * @param string $migration_name
     *
     * @return self
     */
    public function setMigration_name(string $migration_name = null): self
    {
        $this->migration_name = $migration_name;

        return $this;
    }

    public function getStart_time(): ?string
    {
        return $this->start_time;
    }

    /**
     * @param string $start_time
     *
     * @return self
     */
    public function setStart_time(string $start_time = null): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEnd_time(): ?string
    {
        return $this->end_time;
    }

    /**
     * @param string $end_time
     *
     * @return self
     */
    public function setEnd_time(string $end_time = null): self
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getBreakpoint(): ?int
    {
        return $this->breakpoint;
    }

    /**
     * @param int $breakpoint
     *
     * @return self
     */
    public function setBreakpoint(int $breakpoint = null): self
    {
        $this->breakpoint = $breakpoint;

        return $this;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function save(): Models\MigrationsModel
    {
        /** @var TableGateways\MigrationsTableGateway $tableGateway */
        $tableGateway = App::DI(TableGateways\MigrationsTableGateway::class);

        return $tableGateway->save($this);
    }

    /**
     * Destroy the current record.
     * Returns the number of affected rows.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function destroy(): int
    {
        /** @var TableGateways\MigrationsTableGateway $tableGateway */
        $tableGateway = App::DI(TableGateways\MigrationsTableGateway::class);

        return $tableGateway->delete($this->getPrimaryKeys_dbColumns());
    }

    /**
     * Destroy the current record, and any dependencies upon it, recursively.
     * Returns the number of affected rows.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function destroyThoroughly(): int
    {
        return $this->destroy();
    }

    /**
     * Provides an array of all properties in this model.
     *
     * @return string[]
     */
    public function getListOfProperties(): array
    {
        return [
            'version' => 'version',
            'migration_name' => 'migration_name',
            'start_time' => 'start_time',
            'end_time' => 'end_time',
            'breakpoint' => 'breakpoint',
        ];
    }

    /**
     * Take an input array $data, and turn that array into a hydrated object.
     *
     * This has been re-written to be as permissive as possible with loading in data. This at some point will need to
     * be re-re-written as a less messy solution (ie: picking one input field style and sticking with it)
     *
     * @todo re-rewrite this: pick one input field style and stick with it
     *
     * @param array $data dehydated object array
     *
     * @return Models\MigrationsModel
     */
    public function exchangeArray(array $data): self
    {
        if (isset($data['version'])) $this->setVersion($data['version']);
        if (isset($data['version'])) $this->setVersion($data['version']);
        if (isset($data['Version'])) $this->setVersion($data['Version']);
        if (isset($data['migration_name'])) $this->setMigration_name($data['migration_name']);
        if (isset($data['migration_name'])) $this->setMigration_name($data['migration_name']);
        if (isset($data['Migration_name'])) $this->setMigration_name($data['Migration_name']);
        if (isset($data['start_time'])) $this->setStart_time($data['start_time']);
        if (isset($data['start_time'])) $this->setStart_time($data['start_time']);
        if (isset($data['Start_time'])) $this->setStart_time($data['Start_time']);
        if (isset($data['end_time'])) $this->setEnd_time($data['end_time']);
        if (isset($data['end_time'])) $this->setEnd_time($data['end_time']);
        if (isset($data['End_time'])) $this->setEnd_time($data['End_time']);
        if (isset($data['breakpoint'])) $this->setBreakpoint($data['breakpoint']);
        if (isset($data['breakpoint'])) $this->setBreakpoint($data['breakpoint']);
        if (isset($data['Breakpoint'])) $this->setBreakpoint($data['Breakpoint']);
        return $this;
    }

}
