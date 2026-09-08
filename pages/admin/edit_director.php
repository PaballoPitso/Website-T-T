<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM directors WHERE id = $id");
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $d_name = $_POST['name'] ;
    $d_surname = $_POST['surname'] ;
    $d_id_number = $_POST['id_number'];
    $d_street = $_POST['street_address'];
    $d_suburb = $_POST['suburb'] ;
    $d_city = $_POST['city'] ;
    $d_postal = $_POST['postal_code'] ;
    $d_email = $_POST['email'] ;
    $d_cell = $_POST['cellphone'];


    $update = $conn->prepare("UPDATE directors SET name=?, surname=?, id_number=?, street_address=?, suburb=?, city=?, postal_code=?, email=?, cellphone=? WHERE id=?");
    $update->bind_param("sssssssssi", $d_name, $d_surname, $d_id_number, $d_street, $d_suburb, $d_city, $d_postal, $d_email, $d_cell, $id);
    $update->execute();

    header("Location: index.php");
    exit;
}
?>

<h2>Edit Director</h2>
<form method="POST">
    <label>Name: <input type="text" name="name" value="<?= $data['name'] ?>" required></label><br><br>
    <label>Surname: <input type="text" name="surname" value="<?= $data['surname'] ?>" required></label><br><br>
    <label>ID Number: <input type="text" name="id_number" value="<?= $data['id_number'] ?>" required></label><br><br>
    <label>Street Address: <input type="text" name="street_address" value="<?= $data['street_address'] ?>" required></label><br><br>
    <label>Suburb: <input type="text" name="suburb" value="<?= $data['suburb'] ?>" required></label><br><br>
    <label>City: <input type="text" name="city" value="<?= $data['city'] ?>" required></label><br><br>
    <label>Postal Code: <input type="text" name="postal_code" value="<?= $data['postal_code'] ?>" required></label><br><br>
        <label>Email: <input type="email" name="email" value="<?= $data['email'] ?>" required></label><br>

<label>Cellphone: <input type="text" name="cellphone" value="<?= $data['cellphone'] ?>" required></label><br><br>

    <button type="submit">Save Changes</button>
</form>
<a href="index.php">Back</a>
<link rel="stylesheet" href="style.css">
