<?php
namespace Models;

use Core\Db;

class ServiceModel
{
    public static function forCompany($companyId)
    {
        $stmt = Db::getInstance()->prepare('SELECT id, company_id, name, price, description FROM services WHERE company_id = ? ORDER BY id DESC');
        $stmt->execute(array($companyId));
        return $stmt->fetchAll();
    }

    public static function create($companyId, $name, $price, $description)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO services (company_id, name, price, description, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute(array($companyId, $name, $price, $description));
        return Db::getInstance()->lastInsertId();
    }

    public static function update($id, $name, $price, $description)
    {
        $stmt = Db::getInstance()->prepare('UPDATE services SET name = ?, price = ?, description = ? WHERE id = ?');
        $stmt->execute(array($name, $price, $description, $id));
    }

    public static function delete($id)
    {
        $stmt = Db::getInstance()->prepare('DELETE FROM services WHERE id = ?');
        $stmt->execute(array($id));
    }

    public static function findById($id)
    {
        $stmt = Db::getInstance()->prepare('SELECT * FROM services WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->fetch();
    }
}
