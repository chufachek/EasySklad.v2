<?php
$pageTitle = 'Easy склад · Компания';
$page = 'company';
?>
<div class="page-header">
    <div>
        <h1>Компания</h1>
        <p class="muted">Параметры компании, тариф и склады.</p>
    </div>
    <button class="btn btn-primary" id="createWarehouseBtn">Создать склад</button>
</div>
<div class="grid two">
    <div class="card">
        <h2>Информация</h2>
        <div id="companyInfo" class="info-list"></div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2>Склады</h2>
            <span class="pill">Free</span>
        </div>
        <div id="companyWarehouses"></div>
    </div>
</div>
