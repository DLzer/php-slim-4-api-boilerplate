<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ExampleMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('example_migration_table');
        $table->addColumn('user_id', 'integer')
              ->addColumn('first_name', 'string' , ['length' => 50])
              ->addColumn('last_name', 'string', ['length' => 50])
              ->addColumn('email', 'string', ['length' => 50])
              ->addColumn('created_at', 'timestamp', ['default' => NULL])
              ->addIndex(['member_id'])
              ->create();
    }
}
