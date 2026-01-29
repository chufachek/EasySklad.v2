<?php
namespace Models;

use Core\Db;

class OrderModel
{
    public static function listForWarehouse($warehouseId, $status, $limit, $offset)
    {
        $query = 'SELECT id, warehouse_id, company_id, customer_name, payment_method, status, discount, total, created_at FROM orders WHERE warehouse_id = ?';
        $params = array($warehouseId);
        if ($status) {
            $query .= ' AND status = ?';
            $params[] = $status;
        }
        $query .= ' ORDER BY id DESC LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);
        $stmt = Db::getInstance()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create($warehouseId, $companyId, $userId, $customerName, $paymentMethod, $status, $discount, $total)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO orders (warehouse_id, company_id, user_id, customer_name, payment_method, status, discount, total, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute(array($warehouseId, $companyId, $userId, $customerName, $paymentMethod, $status, $discount, $total));
        return Db::getInstance()->lastInsertId();
    }

    public static function addItem($orderId, $productId, $qty, $price, $total)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO order_items (order_id, product_id, qty, price, total) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(array($orderId, $productId, $qty, $price, $total));
    }

    public static function addService($orderId, $serviceId, $qty, $price, $total)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO order_services (order_id, service_id, qty, price, total) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(array($orderId, $serviceId, $qty, $price, $total));
    }

    public static function findById($orderId)
    {
        $stmt = Db::getInstance()->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute(array($orderId));
        return $stmt->fetch();
    }

    public static function updateStatus($orderId, $status)
    {
        $stmt = Db::getInstance()->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute(array($status, $orderId));
    }

    public static function getItems($orderId)
    {
        $stmt = Db::getInstance()->prepare('SELECT oi.id, oi.product_id, p.name as product_name, oi.qty, oi.price, oi.total FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?');
        $stmt->execute(array($orderId));
        return $stmt->fetchAll();
    }

    public static function getServices($orderId)
    {
        $stmt = Db::getInstance()->prepare('SELECT os.id, os.service_id, s.name as service_name, os.qty, os.price, os.total FROM order_services os JOIN services s ON s.id = os.service_id WHERE os.order_id = ?');
        $stmt->execute(array($orderId));
        return $stmt->fetchAll();
    }
}
