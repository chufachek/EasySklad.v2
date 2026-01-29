<?php
$pageTitle = 'Easy склад · Регистрация';
$isAuth = true;
?>
<div class="auth-card">
    <div class="logo">Easy <span>склад</span></div>
    <h1>Регистрация</h1>
    <p class="muted">Создайте аккаунт и начните вести учёт.</p>
    <?php if (!empty($flash)) : ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?php echo base_url('/auth/register'); ?>">
        <label class="field">
            <span>Имя</span>
            <input type="text" name="name" placeholder="Анна" required>
        </label>
        <label class="field">
            <span>Email</span>
            <input type="email" name="email" placeholder="name@company.ru" required>
        </label>
        <label class="field">
            <span>Пароль</span>
            <input type="password" name="password" placeholder="Минимум 8 символов" required>
        </label>
        <button class="btn btn-primary" type="submit">Создать аккаунт</button>
    </form>
    <div class="auth-links">
        <span>Уже есть аккаунт?</span>
        <a href="<?php echo base_url('/login'); ?>">Войти</a>
    </div>
</div>
