<?php

use Benzine\ORM\Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class TestTableStructure extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('Users', ['id' => 'userId'])
            ->addColumn('name', 'string', ['length' => 32])
            ->addIndex(['name'], ['unique' => true])
            ->addColumn('email', 'string', ['length' => 320])
            ->addColumn('created', 'timestamp')
            ->create()
        ;

        $this->table('BlogPosts', ['id' => 'blogPostId'])
            ->addColumn('title', 'string', ['length' => 64])
            ->addIndex(['title'], ['unique' => true])
            ->addColumn('description', 'text')
            ->addColumn('userId', 'integer')
            ->addForeignKey('userId', 'Users', 'userId')
            ->addColumn('created', 'timestamp')
            ->create()
        ;
    }
}
