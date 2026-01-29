<?php
namespace Models;

use Core\Db;

class UserModel
{
    public static function findByEmail($email)
    {
        $stmt = Db::getInstance()->prepare('SELECT id, name, first_name, last_name, username, email, password_hash, tariff, balance FROM users WHERE email = ?');
        $stmt->execute(array($email));
        return $stmt->fetch();
    }

    public static function findByUsername($username)
    {
        $stmt = Db::getInstance()->prepare('SELECT id, name, first_name, last_name, username, email, password_hash, tariff, balance FROM users WHERE username = ?');
        $stmt->execute(array($username));
        return $stmt->fetch();
    }

    public static function findById($id)
    {
        $stmt = Db::getInstance()->prepare('SELECT id, name, first_name, last_name, username, email, tariff, balance FROM users WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO users (name, first_name, last_name, username, email, password_hash, tariff, balance, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute(array(
            $data['name'],
            $data['first_name'],
            $data['last_name'],
            $data['username'],
            $data['email'],
            $data['password_hash'],
            $data['tariff'],
            $data['balance'],
        ));
        return Db::getInstance()->lastInsertId();
    }

    public static function updateProfile($id, $data)
    {
        $stmt = Db::getInstance()->prepare('UPDATE users SET name = ?, first_name = ?, last_name = ?, username = ?, email = ? WHERE id = ?');
        $stmt->execute(array(
            $data['name'],
            $data['first_name'],
            $data['last_name'],
            $data['username'],
            $data['email'],
            $id,
        ));
        return self::findById($id);
    }

    public static function generateUniqueUsername($base)
    {
        $candidate = strtolower($base);
        $candidate = preg_replace('/[^a-z0-9_]/', '', $candidate);
        if ($candidate === '') {
            $candidate = 'user';
        }
        $unique = $candidate;
        $suffix = 1;
        while (self::findByUsername($unique)) {
            $unique = $candidate . $suffix;
            $suffix++;
        }
        return $unique;
    }
}
