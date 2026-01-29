<?php
$pageTitle = 'Easy склад · Услуги';
$page = 'services';
include __DIR__ . '/partials/layout-top.php';
?>
<div class="page-header">
    <div>
        <h1>Услуги</h1>
        <p class="muted">Услуги не привязаны к складам и могут добавляться в заказ.</p>
    </div>
    <button class="btn btn-primary" id="addServiceBtn">Добавить услугу</button>
</div>
<div class="card">
    <div id="servicesTable" class="table-wrap"></div>
</div>
<?php include __DIR__ . '/partials/layout-bottom.php'; ?>
