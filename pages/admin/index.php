<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <a href="logout.php">Logout</a>
    <hr>

    <h2>All Registrations</h2>

    <?php
    $registrations = $conn->query("SELECT * FROM registrations");

    while ($reg = $registrations->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<h3>{$reg['name1']} ({$reg['entity_type']})</h3>";
        echo "<p>Email: {$reg['email']}</p>";
        echo "<a href='edit_registration.php?id={$reg['id']}'>Edit Registration</a> | ";
        echo "<a href='delete.php?type=registration&id={$reg['id']}' onclick='return confirm(\"Delete this registration?\")'>Delete</a>";

        $reg_id = $reg['id'];
        $directors = $conn->query("SELECT * FROM directors WHERE registration_id = $reg_id");

        echo "<ul>";
        while ($dir = $directors->fetch_assoc()) {
            echo "<li>{$dir['name']} {$dir['surname']} 
                (<a href='edit_director.php?id={$dir['id']}'>Edit</a> | 
                <a href='delete.php?type=director&id={$dir['id']}' onclick='return confirm(\"Delete this director?\")'>Delete</a>)</li></br></br>";
        
       
}
 echo "<a href='add_director.php?registration_id={$reg['id']}'>+ Add More Directors</a>";

        echo "</ul>";
        echo "</div><br>";
    }
    ?>
</body>
</html>
