<?php
$pageTitle = 'Easy склад · Товары';
$page = 'products';
include __DIR__ . '/partials/layout-top.php';
?>
<div class="page-header">
    <div>
        <h1>Товары</h1>
        <p class="muted">Каталог товаров и остатки.</p>
    </div>
    <button class="btn btn-primary" id="addProductBtn">Добавить товар</button>
</div>
<div class="card">
    <div class="filters">
        <input type="text" id="productSearch" placeholder="Поиск по названию или артикулу">
        <select id="productFilter" class="choice-select">
            <option value="all">Все</option>
            <option value="low">Низкий остаток</option>
            <option value="high">Хороший остаток</option>
        </select>
    </div>
    <div id="productsTable" class="table-wrap"></div>
</div>
<?php include __DIR__ . '/partials/layout-bottom.php'; ?>
