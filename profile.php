<?php
$pageTitle = 'Easy склад · Профиль';
$page = 'profile';
include __DIR__ . '/partials/layout-top.php';
?>
<div class="page-header">
    <div>
        <h1>Профиль</h1>
        <p class="muted">Управляйте личными данными и компаниями.</p>
    </div>
    <button class="btn btn-primary" id="createCompanyBtn">Создать компанию</button>
</div>
<div class="grid two">
    <div class="card">
        <h2>Информация о пользователе</h2>
        <div class="info-list">
            <div>
                <span class="label">Имя</span>
                <strong>Анна Смирнова</strong>
            </div>
            <div>
                <span class="label">Email</span>
                <strong>anna@easysklad.ru</strong>
            </div>
            <div>
                <span class="label">Роль</span>
                <strong>Владелец</strong>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2>Компании</h2>
            <span class="pill">Free</span>
        </div>
        <div id="profileCompanies"></div>
    </div>
</div>
<?php include __DIR__ . '/partials/layout-bottom.php'; ?>
