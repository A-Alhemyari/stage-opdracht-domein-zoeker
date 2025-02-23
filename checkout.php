<?php
include_once 'modules/database.php';
global $pdo;
//echo '<pre>';
//var_dump($_SESSION['cart']);
//echo '</pre>';

const FNAME_REQUIRED = 'Voornaam invullen';
const LNAME_REQUIRED = 'Achternaam invullen';
const EMAIL_REQUIRED = 'Email invullen';
const EMAIL_INVALID = 'Geldig emailadres invullen';
const POSTCODE_REQUIRED = 'Postcode invullen';
const STREET_REQUIRED = 'Straat invullen';
const CITY_REQUIRED = 'Woonplaats invullen';
const PHONE_REQUIRED = 'Telefoonnummer invullen';
const AGREE_REQUIRED = 'Voorwaarden accepteren';

$errors = [];
$inputs = [];
$totalPrice = 0;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty(trim($fname))) {
        $errors['fname'] = FNAME_REQUIRED;
    } else {
        $inputs['fname'] = $fname;
    }

    $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty(trim($lname))) {
        $errors['lname'] = LNAME_REQUIRED;
    } else {
        $inputs['lname'] = $lname;
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $errors['email'] = EMAIL_INVALID;
    } else {
        $inputs['email'] = $email;
    }

    $postcode = filter_input(INPUT_POST, 'postcode', FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty(trim($postcode))) {
        $errors['postcode'] = POSTCODE_REQUIRED;
    } else {
        $inputs['postcode'] = $postcode;
    }

    $street = filter_input(INPUT_POST, 'street', FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty(trim($street))) {
        $errors['street'] = STREET_REQUIRED;
    } else {
        $inputs['street'] = $street;
    }

    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty(trim($city))) {
        $errors['city'] = CITY_REQUIRED;
    } else {
        $inputs['city'] = $city;
    }

    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty(trim($phone))) {
        $errors['phone'] = PHONE_REQUIRED;
    } else {
        $inputs['phone'] = $phone;
    }

    $agree = filter_input(INPUT_POST, 'agree', FILTER_VALIDATE_BOOLEAN);
    if (!$agree) {
        $errors['agree'] = AGREE_REQUIRED;
    }

    if (empty($errors)) {
        $cartProducts = $_SESSION['cart'] ?? [];
        $totalPrice = 0;

        if (empty($cartProducts)) {
            $errors['cart'] = 'Uw winkelmandje is leeg.';
        } else {
            foreach ($cartProducts as $domain => $details) {
                if (is_array($details) && isset($details['price'])) {
                    $price = $details['price'];
                } else {
                    $price = $details;
                }
                $totalPrice += (float) $price;


                $stmt = $pdo->prepare('INSERT INTO orders ( price, first_name, last_name, email, postcode, street, city, phone, total_price, status, created_at) 
                                       VALUES ( :price, :first_name, :last_name, :email, :postcode, :street, :city, :phone, :total_price, "In behandeling", NOW())');
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':first_name', $inputs['fname']);
                $stmt->bindParam(':last_name', $inputs['lname']);
                $stmt->bindParam(':email', $inputs['email']);
                $stmt->bindParam(':postcode', $inputs['postcode']);
                $stmt->bindParam(':street', $inputs['street']);
                $stmt->bindParam(':city', $inputs['city']);
                $stmt->bindParam(':phone', $inputs['phone']);
                $stmt->bindParam(':total_price', $totalPrice);
                $stmt->execute();
            }

            unset($_SESSION['cart']);

            $_SESSION['message'] = 'Uw bestelling is  geplaatst! Totaalprijs: €' . number_format($totalPrice, 2);
            header('Location: domein.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Afrekenen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1 class="text-center mb-4">Afrekenen</h1>
    <form method="post">
        <div class="mb-3">
            <label>Voornaam</label>
            <input type="text" name="fname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Achternaam</label>
            <input type="text" name="lname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Postcode</label>
            <input type="text" name="postcode" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Straat</label>
            <input type="text" name="street" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Woonplaats</label>
            <input type="text" name="city" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Telefoonnummer</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" name="agree" required>
            <label class="form-check-label">Ik accepteer de voorwaarden</label>
        </div>
        <button type="submit" name="checkout" class="btn btn-success">Bestelling Plaatsen</button>
    </form>
</div>
</body>
</html>
