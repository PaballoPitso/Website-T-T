<?php
$servername = "127.0.0.1";
$username = "root";
$password = "Mysqlpablo1*";
$dbname = "registration";
$port = 3306; // Set MySQL port

// Create connection with specified port
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Insert into registrations table
    $stmt = $conn->prepare("INSERT INTO registrations (name1, name2, name3, name4, entity_type, physical_street, physical_suburb, physical_city, physical_postal_code, postal_street, postal_suburb, postal_city, postal_postal_code, telephone, cellphone, fax, email) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("ssssssssissssssss", $_POST['name1'], $_POST['name2'], $_POST['name3'], $_POST['name4'], $_POST['choice'], $_POST['physical_street'], $_POST['physical_suburb'], $_POST['physical_city'], $_POST['physical_postal_code'], $_POST['postal_street'], $_POST['postal_suburb'], $_POST['postal_city'], $_POST['postal_postal_code'], $_POST['telephone'], $_POST['cellphone'], $_POST['fax'], $_POST['email']);
    
    if ($stmt->execute()) {
        $registration_id = $stmt->insert_id; // Get last inserted ID

        // Insert Directors if provided
        if (isset($_POST['directors']) && is_array($_POST['directors'])) {
            $stmtDirector = $conn->prepare("INSERT INTO directors (registration_id, name, surname, id_number, street_address, suburb, city, postal_code, email, cellphone) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($_POST['directors'] as $director) {
                $stmtDirector->bind_param("isssssssss", $registration_id, $director['name'], $director['surname'], $director['id_number'], $director['street_address'], $director['suburb'], $director['city'], $director['postal_code'], $director['email'], $director['cellphone']);
                $stmtDirector->execute();
            }
            $stmtDirector->close();
        }

        echo "Data stored successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
