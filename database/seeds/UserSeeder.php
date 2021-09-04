<?php


use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {

        // Clean the table out prior to seeding
        $this->execute("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE table users; SET FOREIGN_KEY_CHECKS = 1;");

        // Add two users. A member and an admin.
        $users = $this->table('users');
        $data = [
            [
                'first_name' => 'Alex',
                'last_name' => 'Hamilton',
                'role_id' => 1,
                'email' => 'testadmin@testmail.com',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Sanders',
                'role_id' => 2,
                'email' => 'testmember@testmail.com',
                'created_at' => date('Y-m-d H:i:s')
              ],
        ];

        $users->insert($data)
        ->saveData();
    }
}
