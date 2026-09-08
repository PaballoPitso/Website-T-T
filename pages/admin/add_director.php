<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

$registration_id = $_GET['registration_id'] ?? null;

if (!$registration_id) {
    echo "Missing registration ID.";
    exit;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d_name = $_POST['name'];
    $d_surname = $_POST['surname'];
    $d_id_number = $_POST['id_number'];
    $d_street = $_POST['street_address'];
    $d_suburb = $_POST['suburb'];
    $d_city = $_POST['city'];
    $d_postal = $_POST['postal_code'];
    $d_email = $_POST['email'];
    $d_cell = $_POST['cellphone'];

    $stmt = $conn->prepare("INSERT INTO directors (registration_id, name, surname, id_number, street_address, suburb, city, postal_code, email, cellphone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssiss", $registration_id, $d_name, $d_surname, $d_id_number, $d_street, $d_suburb, $d_city, $d_postal, $d_email, $d_cell);
    
    if ($stmt->execute()) {
        $success = true;
    }
}
?>

<h2>Add Director</h2>

<?php if ($success): ?>
    <div style="color: green; font-weight: bold; margin-bottom: 10px;">
        ✔ Director successfully saved.
    </div>
<?php endif; ?>

<form method="POST">
    <label>Name: <input type="text" name="name" required></label><br><br>
    <label>Surname: <input type="text" name="surname" required></label><br><br>
    <label>ID Number: <input type="number" name="id_number" required></label><br><br>
    <label>Street Address: <input type="text" name="street_address" required></label><br><br>
    <label>Suburb: <input type="text" name="suburb" required></label><br><br>
    <label>City: <input type="text" name="city" required></label><br><br>
    <label>Postal Code: <input type="number" name="postal_code" required></label><br><br>
    <label>Email: <input type="email" name="email" required></label><br><br>
    <label>Cellphone: <input type="number" name="cellphone" required></label><br><br>

    <button type="submit">Add Director</button>
</form>

<a href="index.php?id=<?= $registration_id ?>">Back to Registration</a>
<link rel="stylesheet" href="style.css">
