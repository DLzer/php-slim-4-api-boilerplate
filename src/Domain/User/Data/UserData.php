<?php

namespace App\Domain\User\Data;

use Selective\ArrayReader\ArrayReader;

/**
 * Data Model.
 */
final class UserData
{
    public $id = null;

    public $firstName = null;

    public $lastName = null;

    public $email = null;

    public $roleId = null;

    public $createdAt = null;


    /**
     * The constructor.
     *
     * @param array $data The data
     */
    public function __construct(array $data = [])
    {
        $reader = new ArrayReader($data);

        $this->id           = $reader->findInt('id');
        $this->roleId       = $reader->findInt('role_id');
        $this->firstName    = $reader->findString('first_name');
        $this->lastName     = $reader->findString('last_name');
        $this->email        = $reader->findString('email');
        $this->createdAt    = $reader->findInt('created_at');
    }

}

