<?php
$pageTitle = 'Easy склад · Склады';
$page = 'warehouses';
include __DIR__ . '/partials/layout-top.php';
?>
<div class="page-header">
    <div>
        <h1>Склады</h1>
        <p class="muted">Управляйте складскими площадками.</p>
    </div>
    <button class="btn btn-primary" id="addWarehouseBtn">Добавить склад</button>
</div>
<div class="card">
    <div id="warehousesTable" class="table-wrap"></div>
</div>
<?php include __DIR__ . '/partials/layout-bottom.php'; ?>
