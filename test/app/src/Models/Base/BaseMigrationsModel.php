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

    // PHPType: int. DBType: bigint.
    // Max Length: 9223372036854775807.
    protected ?int $version = null;

    // PHPType: string. DBType: varchar.
    protected ?string $migration_name = null;

    // PHPType: \DateTime. DBType: timestamp.
    protected ?\DateTime $start_time = null;

    // PHPType: \DateTime. DBType: timestamp.
    protected ?\DateTime $end_time = null;

    // PHPType: int. DBType: tinyint.
    // Max Length: 127.
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
     * @param int|null $version
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
     * @param string|null $migration_name
     *
     * @return self
     */
    public function setMigration_name(string $migration_name = null): self
    {
        $this->migration_name = $migration_name;

        return $this;
    }

    public function getStart_time(): ?\DateTime
    {
        return $this->start_time;
    }

    /**
     * @param \DateTime|null $start_time
     *
     * @return self
     */
    public function setStart_time(\DateTime $start_time = null): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEnd_time(): ?\DateTime
    {
        return $this->end_time;
    }

    /**
     * @param \DateTime|null $end_time
     *
     * @return self
     */
    public function setEnd_time(\DateTime $end_time = null): self
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getBreakpoint(): ?int
    {
        return $this->breakpoint;
    }

    /**
     * @param int|null $breakpoint
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
    public function destroyRecursively(): int
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
     * @param array $data dehydated object array
     *
     * @return Models\MigrationsModel
     */
    public function exchangeArray(array $data): self
    {
        return $this
            ->setVersion($data['version'] ?? $data['Version'])
            ->setMigration_name($data['migration_name'] ?? $data['Migration_name'])
            ->setStart_time(\DateTime::createFromFormat("Y-m-d H:i:s", $data['start_time'] ?? $data['Start_time']))
            ->setEnd_time(\DateTime::createFromFormat("Y-m-d H:i:s", $data['end_time'] ?? $data['End_time']))
            ->setBreakpoint($data['breakpoint'] ?? $data['Breakpoint'])
        ;
    }

}
