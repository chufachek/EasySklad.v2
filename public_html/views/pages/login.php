<?php
$pageTitle = 'Easy склад · Вход';
$isAuth = true;
?>
<div class="auth-card">
    <div class="logo">Easy <span>склад</span></div>
    <h1>Вход в аккаунт</h1>
    <p class="muted">Добро пожаловать! Управляйте складом быстро и удобно.</p>
    <?php if (!empty($flash)) : ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?php echo base_url('/auth/login'); ?>">
        <label class="field">
            <span>Email</span>
            <input type="email" name="email" placeholder="name@company.ru" required>
        </label>
        <label class="field">
            <span>Пароль</span>
            <input type="password" name="password" placeholder="••••••••" required>
        </label>
        <button class="btn btn-primary" type="submit">Войти</button>
    </form>
    <div class="auth-links">
        <span>Нет аккаунта?</span>
        <a href="<?php echo base_url('/register'); ?>">Создать</a>
    </div>
</div>
