<?php
namespace Models;

use Core\Db;

class CompanyModel
{
    public static function forUser($userId)
    {
        $stmt = Db::getInstance()->prepare('SELECT c.id, c.name, c.inn, c.address FROM companies c JOIN company_users cu ON cu.company_id = c.id WHERE cu.user_id = ?');
        $stmt->execute(array($userId));
        return $stmt->fetchAll();
    }

    public static function countForUser($userId)
    {
        $stmt = Db::getInstance()->prepare('SELECT COUNT(*) as total FROM company_users WHERE user_id = ?');
        $stmt->execute(array($userId));
        $row = $stmt->fetch();
        return $row ? intval($row['total']) : 0;
    }

    public static function create($name, $inn, $address)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO companies (name, inn, address, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute(array($name, $inn, $address));
        return Db::getInstance()->lastInsertId();
    }

    public static function attachUser($companyId, $userId, $role)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO company_users (company_id, user_id, role) VALUES (?, ?, ?)');
        $stmt->execute(array($companyId, $userId, $role));
    }

    public static function findByIdForUser($companyId, $userId)
    {
        $stmt = Db::getInstance()->prepare('SELECT c.id, c.name, c.inn, c.address FROM companies c JOIN company_users cu ON cu.company_id = c.id WHERE c.id = ? AND cu.user_id = ?');
        $stmt->execute(array($companyId, $userId));
        return $stmt->fetch();
    }

    public static function update($companyId, $name, $inn, $address)
    {
        $stmt = Db::getInstance()->prepare('UPDATE companies SET name = ?, inn = ?, address = ? WHERE id = ?');
        $stmt->execute(array($name, $inn, $address, $companyId));
    }
}
