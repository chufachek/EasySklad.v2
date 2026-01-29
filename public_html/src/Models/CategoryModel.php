<?php
namespace Models;

use Core\Db;

class CategoryModel
{
    public static function forCompany($companyId)
    {
        $stmt = Db::getInstance()->prepare(
            'SELECT c.id, c.company_id, c.name, c.created_at, COUNT(p.id) as products_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             WHERE c.company_id = ?
             GROUP BY c.id
             ORDER BY c.name ASC'
        );
        $stmt->execute(array($companyId));
        return $stmt->fetchAll();
    }

    public static function findById($id)
    {
        $stmt = Db::getInstance()->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public static function findByName($companyId, $name)
    {
        $stmt = Db::getInstance()->prepare('SELECT * FROM categories WHERE company_id = ? AND name = ?');
        $stmt->execute(array($companyId, $name));
        return $stmt->fetch();
    }

    public static function create($companyId, $name)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO categories (company_id, name, created_at) VALUES (?, ?, NOW())');
        $stmt->execute(array($companyId, $name));
        return Db::getInstance()->lastInsertId();
    }

    public static function update($id, $name)
    {
        $stmt = Db::getInstance()->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $stmt->execute(array($name, $id));
    }

    public static function delete($id)
    {
        $stmt = Db::getInstance()->prepare('UPDATE products SET category_id = NULL WHERE category_id = ?');
        $stmt->execute(array($id));
        $stmt = Db::getInstance()->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute(array($id));
    }
}
