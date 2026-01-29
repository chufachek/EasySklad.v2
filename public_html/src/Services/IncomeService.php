<?php
namespace Services;

use Core\Db;
use Models\ProductModel;
use Models\StockModel;

class IncomeService
{
    public static function createIncome($warehouseId, $supplier, $date, $items)
    {
        $pdo = Db::getInstance();
        $pdo->beginTransaction();
        try {
            foreach ($items as $item) {
                $product = ProductModel::findById($item['product_id']);
                if (!$product || intval($product['warehouse_id']) !== intval($warehouseId)) {
                    throw new \Exception('PRODUCT_NOT_FOUND');
                }
                $current = StockModel::getStock($item['product_id'], $warehouseId);
                $newQty = $current + floatval($item['qty']);
                StockModel::setStock($item['product_id'], $warehouseId, $newQty);
                StockModel::addMovement($warehouseId, $item['product_id'], 'in', $item['qty'], $item['cost'], 'income', null, $supplier, $date);
            }
            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
