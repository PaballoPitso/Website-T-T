<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM registrations WHERE id = $id");
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name1 = $_POST['name1'];
    $name2 = $_POST['name2'] ;
    $name3 = $_POST['name3'] ;
    $name4 = $_POST['name4'] ;
    $choice = $_POST['choice'] ;
    $physical_street = $_POST['physical_street'] ;
    $physical_suburb = $_POST['physical_suburb'] ;
    $physical_city = $_POST['physical_city'] ;
    $physical_postal_code = $_POST['physical_postal_code'] ;
    $postal_street = $_POST['postal_street'] ;
    $postal_suburb = $_POST['postal_suburb'];
    $postal_city = $_POST['postal_city'] ;
    $postal_postal_code = $_POST['postal_postal_code'] ;
    $telephone = $_POST['telephone'];
    $cellphone = $_POST['cellphone'] ;
    $fax = $_POST['fax'] ;
    $email = $_POST['email'] ;

    $update = $conn->prepare("UPDATE registrations SET name1=?, name2=?, name3=?, name4=?, entity_type=?, physical_street=?, physical_suburb=?, physical_city=?, physical_postal_code=?, postal_street=?, postal_suburb=?, postal_city=?, postal_postal_code=?, telephone=?, cellphone=?, fax=?, email=? WHERE id=?");
    $update->bind_param("ssssssssissssssssi", $name1, $name2, $name3, $name4, $choice, $physical_street, $physical_suburb, $physical_city, $physical_postal_code, $postal_street, $postal_suburb, $postal_city, $postal_postal_code, $telephone, $cellphone, $fax, $email, $id);
    $update->execute();

    header("Location: index.php");
    exit;
}
?>

<h2>Edit Registration</h2>
<form method="POST">
    <label>Name 1: <input type="text" name="name1" value="<?= $data['name1'] ?>" required></label><br>
    <label>Name 2: <input type="text" name="name2" value="<?= $data['name2'] ?>" required></label><br>
    <label>Name 3: <input type="text" name="name3" value="<?= $data['name3'] ?>" required></label><br> 
    <label>Name 4: <input type="text" name="name4" value="<?= $data['name4'] ?>" ></label><br>
    <label>Entity Type: 
        <select name="choice" required>
            <option value="Private Company(Pty)Ltd" <?= $data['entity_type'] == 'Private Company(Pty)Ltd' ? 'selected' : '' ?>>Private Company(Pty)Ltd</option>
            <option value="Public Company (Ltd)" <?= $data['entity_type'] == 'Public Company (Ltd)' ? 'selected' : '' ?>>Public Company (Ltd)</option>
            <option value="Cooperative" <?= $data['entity_type'] == 'Cooperative' ? 'selected' : '' ?>>Cooperative</option>
        </select>
    </label><br>
    <label>Physical Address Street: <input type="text" name="physical_street" value="<?= $data['physical_street'] ?>" required></label><br>
    <label>Physical Address Suburb: <input type="text" name="physical_suburb" value="<?= $data['physical_suburb'] ?>" required></label><br>
    <label>Physical Address City: <input type="text" name="physical_city" value="<?= $data['physical_city'] ?>" required></label><br>
    <label>Physical Address Postal Code: <input type="text" name="physical_postal_code" value="<?= $data['physical_postal_code'] ?>" required></label><br>
    <label>
                    <input type="checkbox" id="sameAddress" onclick="copyAddress()"> Same as physical address
                </label><br>
    <script>
    function copyAddress() {
        if (document.getElementById('sameAddress').checked) {
            document.querySelector('input[name="postal_street"]').value = document.querySelector('input[name="physical_street"]').value;
            document.querySelector('input[name="postal_suburb"]').value = document.querySelector('input[name="physical_suburb"]').value;
            document.querySelector('input[name="postal_city"]').value = document.querySelector('input[name="physical_city"]').value;
            document.querySelector('input[name="postal_postal_code"]').value = document.querySelector('input[name="physical_postal_code"]').value;
        } else {
            document.querySelector('input[name="postal_street"]').value = '';
            document.querySelector('input[name="postal_suburb"]').value = '';
            document.querySelector('input[name="postal_city"]').value = '';
            document.querySelector('input[name="postal_postal_code"]').value = '';
        }
    }
    </script>
    <label>Postal Address Street: <input type="text" name="postal_street" value="<?= $data['postal_street'] ?>" required></label><br>
    <label>Postal Address Suburb: <input type="text" name="postal_suburb" value="<?= $data['postal_suburb'] ?>" required></label><br>
    <label>Postal Address City: <input type="text" name="postal_city" value="<?= $data['postal_city'] ?>" required></label><br>
    <label>Postal Address Postal Code: <input type="text" name="postal_postal_code" value="<?= $data['postal_postal_code'] ?>" required></label><br>
    <label>Telephone: <input type="text" name="telephone" value="<?= $data['telephone'] ?>" ></label><br>
    <label>Cellphone: <input type="text" name="cellphone" value="<?= $data['cellphone'] ?>" required></label><br>
    <label>Fax: <input type="text" name="fax" value="<?= $data['fax'] ?>" ></label><br>
    <label>Email: <input type="email" name="email" value="<?= $data['email'] ?>" required></label><br>
    
    <button type="submit">Save Changes</button>
</form>
<a href="index.php">Back</a>

<link rel="stylesheet" href="style.css">