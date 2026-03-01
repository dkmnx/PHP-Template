<?php

namespace App\Repositories;

use App\Core\Database;

class UserRepository
{
    public function all()
    {
        return Database::query("SELECT * FROM users");
    }

    public function findById($id)
    {
        return Database::prepare("SELECT * FROM users WHERE id = :id", ['id' => $id]);
    }

    public function findByEmail($email)
    {
        return Database::prepare("SELECT * FROM users WHERE email = :email", ['email' => $email]);
    }
}
