<?php
namespace Models;

use Core\Db;

class WarehouseModel
{
    public static function forCompany($companyId)
    {
        $stmt = Db::getInstance()->prepare('SELECT id, company_id, name, address FROM warehouses WHERE company_id = ?');
        $stmt->execute(array($companyId));
        return $stmt->fetchAll();
    }

    public static function findById($id)
    {
        $stmt = Db::getInstance()->prepare('SELECT id, company_id, name, address FROM warehouses WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public static function create($companyId, $name, $address)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO warehouses (company_id, name, address, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute(array($companyId, $name, $address));
        return Db::getInstance()->lastInsertId();
    }

    public static function update($id, $name, $address)
    {
        $stmt = Db::getInstance()->prepare('UPDATE warehouses SET name = ?, address = ? WHERE id = ?');
        $stmt->execute(array($name, $address, $id));
    }

    public static function delete($id)
    {
        $stmt = Db::getInstance()->prepare('DELETE FROM warehouses WHERE id = ?');
        $stmt->execute(array($id));
    }
}
