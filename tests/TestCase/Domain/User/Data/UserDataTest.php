<?php

namespace App\Test\TestCase\Domain\User\Data;

use App\Domain\User\Data\UserData;
use App\Test\Traits\AppTestTrait;
use PHPUnit\Framework\TestCase;

class UserDataTest extends TestCase
{

    use AppTestTrait;

    public function userProvider()
    {
        return [
            [1, 'Create', 'Event Created', 1, 'TimeNow']
        ];
    }

    public function testSetter()
    {

        $testUser = [
            'id' => 1,
            'role_id' => 1,
            'first_name' => 'Alex',
            'last_name' => 'Hamilton',
            'email' => 'testadmin@testmail.com',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $user = new UserData($testUser);

        $this->assertEquals($user->id, $testUser['id']);
        $this->assertEquals($user->firstName, $testUser['first_name']);
        $this->assertEquals($user->lastName, $testUser['last_name']);
        $this->assertEquals($user->email, $testUser['email']);
        $this->assertEquals($user->roleId, $testUser['role_id']);
        $this->assertEquals($user->createdAt, $testUser['created_at']);

    }

}