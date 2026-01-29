<?php
namespace Models;

use Core\Db;

class UserModel
{
    public static function findByEmail($email)
    {
        $stmt = Db::getInstance()->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ?');
        $stmt->execute(array($email));
        return $stmt->fetch();
    }

    public static function findById($id)
    {
        $stmt = Db::getInstance()->prepare('SELECT id, name, email FROM users WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public static function create($name, $email, $passwordHash)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO users (name, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute(array($name, $email, $passwordHash));
        return Db::getInstance()->lastInsertId();
    }
}
