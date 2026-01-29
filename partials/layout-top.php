<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Easy склад';
}
if (!isset($page)) {
    $page = 'dashboard';
}
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
<body data-page="<?php echo htmlspecialchars($page); ?>">
<div class="app">
    <?php include __DIR__ . '/header.php'; ?>
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="main">
        <div class="breadcrumbs" id="breadcrumbs"></div>
        <section class="content">
