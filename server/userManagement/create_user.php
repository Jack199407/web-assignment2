<?php
// create_user.php: Handles the creation of new users

// Include database connection
require_once '../db.php'; 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read raw JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Validate JSON decoding
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            "code" => 1,
            "message" => "Invalid JSON input.",
            "data" => null
        ]);
        exit;
    }

    // Read and validate input
    $loginName = trim($data['loginName'] ?? '');
    $passwd = trim($data['password'] ?? ''); // 'password' key to match the API input
    $email = trim($data['email'] ?? '');
    $firstName = trim($data['firstName'] ?? '');
    $lastName = trim($data['lastName'] ?? '');

    // Initialize response format
    $response = [
        "code" => 1,
        "message" => "Validation errors occurred.",
        "data" => null,
        "errors" => [] // Collect all errors here
    ];

    // Validation rules
    if (empty($loginName)) {
        $response["errors"][] = 'loginName is required.';
    } elseif (strlen($loginName) > 50) {
        $response["errors"][] = 'loginName cannot exceed 50 characters.';
    }

    if (empty($passwd)) {
        $response["errors"][] = 'password is required.';
    } elseif (strlen($passwd) > 255) {
        $response["errors"][] = 'password cannot exceed 255 characters.';
    }

    if (empty($email)) {
        $response["errors"][] = 'email is required.';
    } elseif (strlen($email) > 100) {
        $response["errors"][] = 'email cannot exceed 100 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["errors"][] = 'Invalid email format.';
    }

    if (!empty($firstName) && strlen($firstName) > 50) {
        $response["errors"][] = 'firstName cannot exceed 50 characters.';
    }

    if (!empty($lastName) && strlen($lastName) > 50) {
        $response["errors"][] = 'lastName cannot exceed 50 characters.';
    }

    // Check if there are validation errors
    if (!empty($response["errors"])) {
        echo json_encode($response);
        exit;
    }

    try {
        // Check if loginName already exists
        $checkLoginStmt = $db->prepare('SELECT COUNT(*) FROM users WHERE loginName = :loginName');
        $checkLoginStmt->bindParam(':loginName', $loginName);
        $checkLoginStmt->execute();
        if ($checkLoginStmt->fetchColumn() > 0) {
            $response["errors"][] = 'A user with the same loginName already exists.';
        }

        // Check if email already exists
        $checkEmailStmt = $db->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $checkEmailStmt->bindParam(':email', $email);
        $checkEmailStmt->execute();
        if ($checkEmailStmt->fetchColumn() > 0) {
            $response["errors"][] = 'A user with the same email already exists.';
        }

        // Check for any errors after database checks
        if (!empty($response["errors"])) {
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
        $response["errors"] = null;
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
