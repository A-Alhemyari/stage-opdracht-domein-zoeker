<?php

session_start();

if (isset($_POST['domeinnaam'])) {
    $domeinnaam = trim($_POST['domeinnaam']);
    $tlds = ['com', 'nl', 'org', 'net', 'info', 'biz', 'eu', 'co.uk', 'de', 'fr'];
    $domains = [];

    foreach ($tlds as $tld) {
        $domains[] = ["name" => $domeinnaam, "extension" => $tld];
    }

    $ch = curl_init('https://dev.api.mintycloud.nl/api/v2.1/domains/search?with_price=true');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic 072dee999ac1a7931c205814c97cb1f4d1261559c0f6cd15f2a7b27701954b8d',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($domains));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
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
                <li class="nav-item"><a class="nav-link" href="orders.php">Bestellen</a></li>
            </ul>
        </div>
    </div>
</nav>
    <?php if(!empty($_SESSION['message'])): ?>
    <div class="alert alert-success" role="alert">
        <?=$_SESSION['message']?>
        <?php $_SESSION['message']=null;?>
    </div>
    <?php endif;?>
<div class="container mt-5">
    <h1 class="mb-4">Domein Zoeken</h1>
    <form method="post" class="mb-4">
        <div class="input-group">
            <input type="text" name="domeinnaam" class="form-control" placeholder="Voer een domeinnaam in" required>
            <button type="submit" class="btn btn-danger">Zoek</button>
        </div>
    </form>

    <?php if (!empty($result)): ?>
        <table class="table ">
            <thead>
            <tr>
                <th>Domeinnaam</th>
                <th>Status</th>
                <th>Prijs</th>
                <th>Actie</th>
            </tr>
            </thead>
            <tbody>

            <?php

            foreach ($result as $domein): ?>
                <tr>
                    <td><?= ($domein['domain']) ?></td>
                    <td><?= $domein['status'] === 'free' ? 'Beschikbaar' : 'Niet beschikbaar' ?></td>
                    <td>
                        <?php
                        $prijs = 0;
                        foreach ($domein['price'] as $product) {
                            $prijs = $product['price'];
                        }
                        echo '€' . number_format($prijs, 2, ',', '.');
                        ?>
                    </td>

                    <td>
                        <?php if ($domein['status'] === 'free'): ?>
                            <a href="cart.php?action=add&domain=<?= ($domein['domain']) ?>&price=<?= ($prijs) ?>" class="btn btn-danger">
                                Voeg toe aan winkelmandje
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Niet beschikbaar</button>
                        <?php endif; ?>
                    </td>


                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>