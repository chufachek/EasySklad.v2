<?php
$pageTitle = 'Easy склад · Регистрация';
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth">
<div class="auth-card">
    <div class="logo">Easy <span>склад</span></div>
    <h1>Регистрация</h1>
    <p class="muted">Создайте аккаунт и начните вести учёт.</p>
    <?php if (!empty($flash)) : ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="/auth/register">
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
        <a href="/login">Войти</a>
    </div>
</div>
</body>
</html>
