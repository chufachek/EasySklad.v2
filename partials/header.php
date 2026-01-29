<header class="header">
    <div class="logo">Easy <span>склад</span></div>
    <div class="header-selects">
        <label class="field">
            <span>Компания</span>
            <select id="companySelect"></select>
        </label>
        <label class="field">
            <span>Склад</span>
            <select id="warehouseSelect"></select>
        </label>
    </div>
    <div class="header-actions">
        <a class="btn btn-primary" href="/income.php">+ Приход</a>
        <a class="btn btn-secondary" href="/orders.php">+ Заказ</a>
        <a class="btn btn-ghost" href="/pos.php">Касса</a>
    </div>
    <div class="header-right">
        <button class="theme-toggle" id="themeToggle" aria-label="Переключить тему">
            <span class="theme-icon">🌙</span>
        </button>
        <div class="profile-menu">
            <button class="profile-trigger" id="profileTrigger">Анна Смирнова ▾</button>
            <div class="profile-dropdown" id="profileDropdown">
                <a href="/profile.php">Профиль</a>
                <a href="/company.php">Тариф</a>
                <a href="/login.php">Выход</a>
            </div>
        </div>
    </div>
</header>
