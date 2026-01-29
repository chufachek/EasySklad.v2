<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Easy склад';
}
if (!isset($page)) {
    $page = 'dashboard';
}
$isAuth = !empty($isAuth);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo base_url('/assets/css/styles.css'); ?>">
    <?php if (!$isAuth) : ?>
        <link rel="stylesheet" href="<?php echo base_url('/assets/styles/choices-theme.css'); ?>">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body <?php echo $isAuth ? 'class="auth"' : 'data-page="' . htmlspecialchars($page) . '"'; ?>>
<?php if (!$isAuth) : ?>
<div class="app">
<?php endif; ?>
