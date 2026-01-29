<?php
$pageTitle = 'Easy склад · Категории';
$page = 'categories';
include __DIR__ . '/partials/layout-top.php';
?>
<div class="page-header">
    <div>
        <h1>Категории</h1>
        <p class="muted">Управление категориями товаров.</p>
    </div>
    <button class="btn btn-primary" id="addCategoryBtn">Создать категорию</button>
</div>
<div class="card">
    <div id="categoriesTable" class="table-wrap"></div>
</div>
<?php include __DIR__ . '/partials/layout-bottom.php'; ?>
