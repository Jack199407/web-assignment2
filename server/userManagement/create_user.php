<?php
// create_user.php: Handles the creation of new users

// Include database connection
require_once '../db.php'; 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and validate input
    $loginName = trim($_POST['loginName'] ?? '');
    $passwd = trim($_POST['password'] ?? ''); // 'password' key to match the API input
    $email = trim($_POST['email'] ?? '');
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');

    // Initialize response format
    $response = [
        "code" => 1,
        "message" => "User creation failed.",
        "data" => null
    ];

    // Validation rules
    if (empty($loginName)) {
        $response["message"] = 'loginName is required.';
        echo json_encode($response);
        exit;
    } elseif (strlen($loginName) > 50) {
        $response["message"] = 'loginName cannot exceed 50 characters.';
        echo json_encode($response);
        exit;
    }

    if (empty($passwd)) {
        $response["message"] = 'password is required.';
        echo json_encode($response);
        exit;
    } elseif (strlen($passwd) > 255) {
        $response["message"] = 'password cannot exceed 255 characters.';
        echo json_encode($response);
        exit;
    }

    if (empty($email)) {
        $response["message"] = 'email is required.';
        echo json_encode($response);
        exit;
    } elseif (strlen($email) > 100) {
        $response["message"] = 'email cannot exceed 100 characters.';
        echo json_encode($response);
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["message"] = 'Invalid email format.';
        echo json_encode($response);
        exit;
    }

    if (!empty($firstName) && strlen($firstName) > 50) {
        $response["message"] = 'firstName cannot exceed 50 characters.';
        echo json_encode($response);
        exit;
    }

    if (!empty($lastName) && strlen($lastName) > 50) {
        $response["message"] = 'lastName cannot exceed 50 characters.';
        echo json_encode($response);
        exit;
    }

    try {
        // Check if loginName or email already exists
        $checkStmt = $db->prepare('SELECT COUNT(*) FROM users WHERE loginName = :loginName OR email = :email');
        $checkStmt->bindParam(':loginName', $loginName);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        $existingCount = $checkStmt->fetchColumn();

        if ($existingCount > 0) {
            $response["message"] = 'A user with the same loginName or email already exists.';
            echo json_encode($response);
            exit;
        }

        // Hash the password for security
        $hashedPassword = password_hash($passwd, PASSWORD_BCRYPT);

        // Insert the user into the database
        $stmt = $db->prepare('INSERT INTO users (loginName, passwd, email, firstName, lastName) 
                              VALUES (:loginName, :passwd, :email, :firstName, :lastName)');
        $stmt->bindParam(':loginName', $loginName);
        $stmt->bindParam(':passwd', $hashedPassword);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);

        $stmt->execute();

        // Update response for success
        $response["code"] = 0;
        $response["message"] = "User successfully created.";
        $response["data"] = null;

        echo json_encode($response);
    } catch (PDOException $e) {
        $response["message"] = 'Database error: ' . $e->getMessage();
        echo json_encode($response);
    }
} else {
    // Handle unsupported request methods
    $response = [
        "code" => 1,
        "message" => "Only POST method is allowed.",
        "data" => null
    ];
    echo json_encode($response);
}
?>
