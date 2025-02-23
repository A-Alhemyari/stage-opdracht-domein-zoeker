<?php
include_once 'modules/database.php';
global $pdo;

$stmt = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC');
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bestellingen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="nav-item"><a class="nav-link" href="orders.php">Bestellen</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-5">
    <h1 class="text-center mb-4">Alle Bestellingen</h1>
    <table class="table">
        <thead>
        <tr>
            <th>Naam</th>
            <th>Email</th>
            <th>Prijs</th>
            <th>Status</th>
            <th>Datum</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['first_name'] . ' ' . $order['last_name'] ?></td>
                <td><?= $order['email'] ?></td>
                <td>€<?= number_format($order['price'], 2) ?></td>

                <td><?= $order['status'] ?></td>
                <td><?= $order['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
