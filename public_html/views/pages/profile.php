<?php
$pageTitle = 'Easy склад · Профиль';
$page = 'profile';
?>
<div class="page-header">
    <div>
        <h1>Личный аккаунт</h1>
        <p class="muted">Обновите персональные данные и проверьте статус аккаунта.</p>
    </div>
    <div class="badge badge-soft">MVP</div>
</div>
<div class="grid two">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Персональные данные</h2>
                <p class="muted">Эти данные видны в интерфейсе и документах.</p>
            </div>
        </div>
        <form id="profileForm" class="stacked-form">
            <label class="field">
                <span>Email</span>
                <input type="email" name="email" id="profileEmail" placeholder="name@company.ru" required>
                <small class="field-error" data-error-for="email"></small>
            </label>
            <div class="grid two">
                <label class="field">
                    <span>Имя</span>
                    <input type="text" name="first_name" id="profileFirstName" placeholder="Анна" required>
                    <small class="field-error" data-error-for="first_name"></small>
                </label>
                <label class="field">
                    <span>Фамилия</span>
                    <input type="text" name="last_name" id="profileLastName" placeholder="Смирнова">
                    <small class="field-error" data-error-for="last_name"></small>
                </label>
            </div>
            <label class="field">
                <span>Username</span>
                <input type="text" name="username" id="profileUsername" placeholder="anna_sklad" required>
                <small class="field-hint">Латиница, цифры и underscore. 3–32 символа.</small>
                <small class="field-error" data-error-for="username"></small>
            </label>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Сохранить</button>
                <button class="btn btn-ghost" type="button" id="profileCancel">Отмена</button>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Данные аккаунта</h2>
                <p class="muted">Тариф и текущее состояние.</p>
            </div>
        </div>
        <div class="info-list">
            <div>
                <span class="label">User ID</span>
                <strong id="profileCardUserId">—</strong>
            </div>
            <div>
                <span class="label">Тариф</span>
                <strong id="profileCardTariff">Free</strong>
            </div>
            <div>
                <span class="label">Баланс</span>
                <strong id="profileCardBalance">0 ₽</strong>
            </div>
        </div>
        <div class="divider"></div>
        <div class="card-header">
            <div>
                <h3>Безопасность</h3>
                <p class="muted">Смена пароля будет доступна позже.</p>
            </div>
            <button class="btn btn-secondary" type="button" disabled>Сменить пароль</button>
        </div>
    </div>
</div>
