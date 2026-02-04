<!DOCTYPE html>
<html>

<head>
    <title><?= e($title ?? 'PHP-Template') ?></title>
    <link rel="stylesheet" href="/iQMS/public/assets/css/main.css">
</head>

<body>

    <div class="layout">

        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <?php require __DIR__ . '/../partials/navbar.php'; ?>

        <main class="content">
            <?= e($content) ?>
        </main>

        <?php require __DIR__ . '/../partials/footer.php'; ?>

    </div>

</body>

</html>