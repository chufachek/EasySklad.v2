<?php
namespace Services;

use Core\Db;
use Models\ProductModel;
use Models\StockModel;
use Models\OrderModel;

class OrderService
{
    public static function createOrder($warehouseId, $companyId, $userId, $payload)
    {
        $pdo = Db::getInstance();
        $pdo->beginTransaction();
        try {
            $total = 0;
            $itemsTotal = 0;
            if (!empty($payload['items'])) {
                foreach ($payload['items'] as $item) {
                    $product = ProductModel::findById($item['product_id']);
                    if (!$product || intval($product['warehouse_id']) !== intval($warehouseId)) {
                        throw new \Exception('PRODUCT_NOT_FOUND');
                    }
                    $stock = StockModel::getStock($item['product_id'], $warehouseId);
                    if ($stock < floatval($item['qty'])) {
                        throw new \Exception('INSUFFICIENT_STOCK');
                    }
                    $lineTotal = floatval($item['qty']) * floatval($item['price']);
                    $itemsTotal += $lineTotal;
                }
            }
            $servicesTotal = 0;
            if (!empty($payload['services'])) {
                foreach ($payload['services'] as $service) {
                    $servicesTotal += floatval($service['qty']) * floatval($service['price']);
                }
            }
            $discount = isset($payload['discount']) ? floatval($payload['discount']) : 0;
            $total = max(0, $itemsTotal + $servicesTotal - $discount);

            $orderId = OrderModel::create(
                $warehouseId,
                $companyId,
                $userId,
                $payload['customer_name'],
                $payload['payment_method'],
                'draft',
                $discount,
                $total
            );

            if (!empty($payload['items'])) {
                foreach ($payload['items'] as $item) {
                    $lineTotal = floatval($item['qty']) * floatval($item['price']);
                    OrderModel::addItem($orderId, $item['product_id'], $item['qty'], $item['price'], $lineTotal);
                    $stock = StockModel::getStock($item['product_id'], $warehouseId);
                    StockModel::setStock($item['product_id'], $warehouseId, $stock - floatval($item['qty']));
                    StockModel::addMovement($warehouseId, $item['product_id'], 'out', $item['qty'], $item['price'], 'order', $orderId, null, date('Y-m-d'));
                }
            }

            if (!empty($payload['services'])) {
                foreach ($payload['services'] as $service) {
                    $lineTotal = floatval($service['qty']) * floatval($service['price']);
                    OrderModel::addService($orderId, $service['service_id'], $service['qty'], $service['price'], $lineTotal);
                }
            }

            $pdo->commit();

            $order = OrderModel::findById($orderId);
            $order['items'] = OrderModel::getItems($orderId);
            $order['services'] = OrderModel::getServices($orderId);
            return $order;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
