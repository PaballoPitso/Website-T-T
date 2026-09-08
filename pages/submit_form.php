<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Load PHPMailer
require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

// DB connection
$servername = "127.0.0.1";
$username = "root";
$password = "Mysqlpablo1*";
$dbname = "registration";
$port = 3306;
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Required fields
$requiredFields = ['name1', 'name2', 'name3', 'choice', 'physical_street', 'physical_city', 'cellphone', 'email'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) $missingFields[] = $field;
}

if (!empty($missingFields)) {
    echo json_encode(["status" => "error", "message" => "Please fill in all required fields: " . implode(", ", $missingFields)]);
    exit;
}

// Collect data
$name1 = $_POST['name1'] ?? '';
$name2 = $_POST['name2'] ?? '';
$name3 = $_POST['name3'] ?? '';
$name4 = $_POST['name4'] ?? '';
$choice = $_POST['choice'] ?? '';
$physical_street = $_POST['physical_street'] ?? '';
$physical_suburb = $_POST['physical_suburb'] ?? '';
$physical_city = $_POST['physical_city'] ?? '';
$physical_postal_code = $_POST['physical_postal_code'] ?? 0;
$postal_street = $_POST['postal_street'] ?? '';
$postal_suburb = $_POST['postal_suburb'] ?? '';
$postal_city = $_POST['postal_city'] ?? '';
$postal_postal_code = $_POST['postal_postal_code'] ?? 0;
$telephone = $_POST['telephone'] ?? '';
$cellphone = $_POST['cellphone'] ?? '';
$fax = $_POST['fax'] ?? '';
$email = $_POST['email'] ?? '';

// ✅ Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email address."]);
    exit;
}

// Store registration
$stmt = $conn->prepare("INSERT INTO registrations (name1, name2, name3, name4, entity_type, physical_street, physical_suburb, physical_city, physical_postal_code, postal_street, postal_suburb, postal_city, postal_postal_code, telephone, cellphone, fax, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssissssssss", $name1, $name2, $name3, $name4, $choice, $physical_street, $physical_suburb, $physical_city, $physical_postal_code, $postal_street, $postal_suburb, $postal_city, $postal_postal_code, $telephone, $cellphone, $fax, $email);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Failed to store registration data: " . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

$registration_id = $stmt->insert_id;
$stmt->close();

// Store directors + attach files
$directors = $_POST['directors'] ?? [];
$directorDetails = "<h3>Directors:</h3>";
$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

$stmtDir = $conn->prepare("INSERT INTO directors (registration_id, name, surname, id_number, street_address, suburb, city, postal_code, email, cellphone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'pabloslaeger@gmail.com';
    $mail->Password = 'lyzf kjgr ffxm uugt';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('pabloslaeger@gmail.com', 'Tee n Tee Registration');
    $mail->addAddress('pabloslaeger@gmail.com');

    // Go through all directors
    foreach ($directors as $index => $dir) {
        $d_name = $dir['name'] ?? '';
        $d_surname = $dir['surname'] ?? '';
        $d_id_number = $dir['id_number'] ?? '';
        $d_street = $dir['street_address'] ?? '';
        $d_suburb = $dir['suburb'] ?? '';
        $d_city = $dir['city'] ?? '';
        $d_postal = $dir['postal_code'] ?? '';
        $d_email = $dir['email'] ?? '';
        $d_cell = $dir['cellphone'] ?? '';

        // Store director in DB
        $stmtDir->bind_param("isssssssss", $registration_id, $d_name, $d_surname, $d_id_number, $d_street, $d_suburb, $d_city, $d_postal, $d_email, $d_cell);
        $stmtDir->execute();

        // Handle director file
        if (isset($_FILES['director_files']['name'][$index]) && $_FILES['director_files']['error'][$index] === UPLOAD_ERR_OK) {
            $fileName = basename($_FILES['director_files']['name'][$index]);
            $targetPath = $uploadDir . time() . "_dir{$index}_" . $fileName;
            if (move_uploaded_file($_FILES['director_files']['tmp_name'][$index], $targetPath)) {
                $mail->addAttachment($targetPath, "Director_{$index}_Document");
            }
        }

        // Email content for director
        $directorDetails .= "
            <hr>
            <p><strong>Director " . ($index + 1) . ":</strong></p>
            <p>Name: $d_name</p>
            <p>Surname: $d_surname</p>
            <p>ID No.: $d_id_number</p>
            <p>Street Address: $d_street</p>
            <p>Suburb: $d_suburb</p>
            <p>City: $d_city</p>
            <p>Postal Code: $d_postal</p>
            <p>Email: $d_email</p>
            <p>Cellphone: $d_cell</p>";
    }
    $stmtDir->close();
    $conn->close();

    // Attach any additional shared files (if any)
    if (!empty($_FILES['files']['name'][0])) {
        foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = basename($_FILES['files']['name'][$key]);
                $targetPath = $uploadDir . time() . "_other_" . $fileName;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $mail->addAttachment($targetPath);
                }
            }
        }
    }

    // Send email
    $mail->isHTML(true);
    $mail->Subject = "New Co-Operative Registration Form Submission";
    $mail->Body = "
        <h2>New Registration Details</h2>
        <p><strong>Name 1:</strong> $name1</p>
        <p><strong>Name 2:</strong> $name2</p>
        <p><strong>Name 3:</strong> $name3</p>
        <p><strong>Name 4:</strong> $name4</p>
        <p><strong>Entity Type:</strong> $choice</p>
        <h3>Physical Address:</h3>
        <p>Street: $physical_street</p>
        <p>Suburb: $physical_suburb</p>
        <p>City: $physical_city</p>
        <p>Postal Code: $physical_postal_code</p>
        <h3>Postal Address:</h3>
        <p>Street: $postal_street</p>
        <p>Suburb: $postal_suburb</p>
        <p>City: $postal_city</p>
        <p>Postal Code: $postal_postal_code</p>
        <h3>Contact Information:</h3>
        <p>Telephone: $telephone</p>
        <p>Cellphone: $cellphone</p>
        <p>Fax: $fax</p>
        <p>Email: $email</p>
        $directorDetails
    ";
    $mail->send();
    echo json_encode(["status" => "success", "message" => "Your form has been successfully submitted, stored and emailed!"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>
