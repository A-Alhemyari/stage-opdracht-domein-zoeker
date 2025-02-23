<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['action']) && $_GET['action'] == "add" && isset($_GET['domain']) && isset($_GET['price'])) {
    $domain = $_GET['domain'];
    $price = floatval($_GET['price']);

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['domain'] === $domain) {
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = ['domain' => $domain, 'price' => $price];
    }

    header("Location: cart.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == "remove" && isset($_GET['domain'])) {
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($item) {
        return $item['domain'] !== $_GET['domain'];
    });

    header("Location: cart.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == "clear") {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit();
}

$subtotaal = array_sum(array_column($_SESSION['cart'], 'price'));
$btw = $subtotaal * 0.21;
$totaal = $subtotaal + $btw;
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winkelmand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="domein.php">Domein Zoeker</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="domein.php">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="cart.php">Winkelmand</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="mb-4">Winkelmand</h1>

    <?php if (!empty($_SESSION['cart'])): ?>
        <table class="table">
            <thead>
            <tr>
                <th>Domeinnaam</th>
                <th>Prijs</th>
                <th>Actie</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <tr>
                    <td><?= ($item['domain']) ?></td>
                    <td>€<?= number_format($item['price'], 2, ',', '.') ?></td>
                    <td>
                        <a href="cart.php?action=remove&domain=<?= urlencode($item['domain']) ?>" class="btn btn-danger">Verwijder</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h4>Subtotaal: €<?= number_format($subtotaal, 2, ',', '.') ?></h4>
        <h4>BTW (21%): €<?= number_format($btw, 2, ',', '.') ?></h4>
        <h3>Totaal: €<?= number_format($totaal, 2, ',', '.') ?></h3>

        <a href="checkout.php" class="btn btn-success">Afrekenen</a>
        <a href="cart.php?action=clear" class="btn btn-warning">Winkelmand legen</a>

    <?php else: ?>
        <p>Je winkelmand is leeg.</p>
    <?php endif; ?>
</div>

</body>
</html>
