<?php
namespace Models;

use Core\Db;

class StockModel
{
    public static function getStock($productId, $warehouseId)
    {
        $stmt = Db::getInstance()->prepare('SELECT qty FROM product_stocks WHERE product_id = ? AND warehouse_id = ?');
        $stmt->execute(array($productId, $warehouseId));
        $row = $stmt->fetch();
        return $row ? floatval($row['qty']) : 0;
    }

    public static function setStock($productId, $warehouseId, $qty)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO product_stocks (product_id, warehouse_id, qty) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE qty = VALUES(qty)');
        $stmt->execute(array($productId, $warehouseId, $qty));
    }

    public static function addMovement($warehouseId, $productId, $type, $qty, $cost, $referenceType, $referenceId, $supplier, $movementDate)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO stock_movements (warehouse_id, product_id, type, qty, cost, reference_type, reference_id, supplier, movement_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute(array($warehouseId, $productId, $type, $qty, $cost, $referenceType, $referenceId, $supplier, $movementDate));
    }

    public static function incomeList($warehouseId, $limit, $offset)
    {
        $stmt = Db::getInstance()->prepare('SELECT sm.id, sm.product_id, p.name as product_name, sm.qty, sm.cost, sm.supplier, sm.movement_date, sm.created_at FROM stock_movements sm JOIN products p ON p.id = sm.product_id WHERE sm.warehouse_id = ? AND sm.type = "in" ORDER BY sm.id DESC LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset));
        $stmt->execute(array($warehouseId));
        return $stmt->fetchAll();
    }
}
