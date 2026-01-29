<?php
namespace Models;

use Core\Db;

class ProductModel
{
    public static function forWarehouse($warehouseId, $search, $limit, $offset)
    {
        $query = 'SELECT p.id, p.warehouse_id, p.category_id, p.sku, p.name, p.price, p.cost, p.unit, p.min_stock, s.qty,
                c.name as category_name
            FROM products p
            LEFT JOIN product_stocks s ON s.product_id = p.id AND s.warehouse_id = p.warehouse_id
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.warehouse_id = ?';
        $params = array($warehouseId);
        if ($search) {
            $query .= ' AND (p.name LIKE ? OR p.sku LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
        }
        $query .= ' ORDER BY p.id DESC LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);
        $stmt = Db::getInstance()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function search($warehouseId, $query)
    {
        $stmt = Db::getInstance()->prepare('SELECT p.id, p.sku, p.name, p.price, s.qty FROM products p LEFT JOIN product_stocks s ON s.product_id = p.id AND s.warehouse_id = p.warehouse_id WHERE p.warehouse_id = ? AND (p.name LIKE ? OR p.sku LIKE ?) ORDER BY p.name ASC LIMIT 20');
        $like = '%' . $query . '%';
        $stmt->execute(array($warehouseId, $like, $like));
        return $stmt->fetchAll();
    }

    public static function create($warehouseId, $categoryId, $sku, $name, $price, $cost, $unit, $minStock)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO products (warehouse_id, category_id, sku, name, price, cost, unit, min_stock, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute(array($warehouseId, $categoryId, $sku, $name, $price, $cost, $unit, $minStock));
        return Db::getInstance()->lastInsertId();
    }

    public static function update($id, $categoryId, $sku, $name, $price, $cost, $unit, $minStock)
    {
        $stmt = Db::getInstance()->prepare('UPDATE products SET category_id = ?, sku = ?, name = ?, price = ?, cost = ?, unit = ?, min_stock = ? WHERE id = ?');
        $stmt->execute(array($categoryId, $sku, $name, $price, $cost, $unit, $minStock, $id));
    }

    public static function findById($id)
    {
        $stmt = Db::getInstance()->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public static function findBySku($warehouseId, $sku)
    {
        $stmt = Db::getInstance()->prepare('SELECT id FROM products WHERE warehouse_id = ? AND sku = ?');
        $stmt->execute(array($warehouseId, $sku));
        return $stmt->fetch();
    }
}
