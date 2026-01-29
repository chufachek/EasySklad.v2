<?php
$pageTitle = 'Easy склад · Вход';
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
    <h1>Вход в аккаунт</h1>
    <p class="muted">Добро пожаловать! Управляйте складом быстро и удобно.</p>
    <form>
        <label class="field">
            <span>Email</span>
            <input type="email" placeholder="name@company.ru" required>
        </label>
        <label class="field">
            <span>Пароль</span>
            <input type="password" placeholder="••••••••" required>
        </label>
        <button class="btn btn-primary" type="submit">Войти</button>
    </form>
    <div class="auth-links">
        <span>Нет аккаунта?</span>
        <a href="/register.php">Создать</a>
    </div>
</div>
</body>
</html>
