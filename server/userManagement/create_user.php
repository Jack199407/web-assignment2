<?php
// create_user.php: Handles the creation of new users

// Include database connection
require_once '../db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and validate input
    $loginName = trim($_POST['loginName'] ?? '');
    $passwd = trim($_POST['passwd'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');

    // Validation rules
    $errors = [];
    if (empty($loginName)) {
        $errors[] = 'loginName is required.';
    } elseif (strlen($loginName) > 50) {
        $errors[] = 'loginName cannot exceed 50 characters.';
    }

    if (empty($passwd)) {
        $errors[] = 'passwd is required.';
    } elseif (strlen($passwd) > 255) {
        $errors[] = 'passwd cannot exceed 255 characters.';
    }

    if (empty($email)) {
        $errors[] = 'email is required.';
    } elseif (strlen($email) > 100) {
        $errors[] = 'email cannot exceed 100 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if (!empty($firstName) && strlen($firstName) > 50) {
        $errors[] = 'firstName cannot exceed 50 characters.';
    }

    if (!empty($lastName) && strlen($lastName) > 50) {
        $errors[] = 'lastName cannot exceed 50 characters.';
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['errors' => $errors]);
        exit;
    }

    try {
        // Hash the password for security
        $hashedPassword = password_hash($passwd, PASSWORD_BCRYPT);

        // Prepare the SQL statement to prevent SQL injection
        $stmt = $db->prepare('INSERT INTO users (loginName, passwd, email, firstName, lastName) 
                              VALUES (:loginName, :passwd, :email, :firstName, :lastName)');
        $stmt->bindParam(':loginName', $loginName);
        $stmt->bindParam(':passwd', $hashedPassword);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);

        // Execute the statement
        $stmt->execute();

        // Send success response
        http_response_code(201); // HTTP 201 Created
        echo json_encode([
            'message' => 'User created successfully.',
            'userId' => $db->lastInsertId(), // Return the new user's ID
        ]);
    } catch (PDOException $e) {
        // Handle duplicate entry or other database errors
        if ($e->getCode() === '23000') {
            http_response_code(409); // HTTP 409 Conflict
            echo json_encode(['error' => 'A user with the same loginName or email already exists.']);
        } else {
            http_response_code(500); // HTTP 500 Internal Server Error
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
} else {
    // Handle unsupported request methods
    http_response_code(405); // HTTP 405 Method Not Allowed
    echo json_encode(['error' => 'Only POST method is allowed.']);
}
?>
