<?php
/*
Name: Gao Miaomiao 041135845
Created Date: 2024-11-21
login.php: Handles user authentication
*/

// Include database connection
require_once 'db.php'; 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and decode JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Read and validate input
    $loginName = trim($input['loginName'] ?? '');
    $passwd = trim($input['password'] ?? ''); // Use 'password' to match JSON key

    // Initialize the response format
    $response = [
        "code" => 1,
        "message" => "Login failed.",
        "data" => null
    ];

    // Basic validation
    $errors = [];
    if (empty($loginName)) {
        $errors[] = "loginName is required.";
    }
    if (empty($passwd)) {
        $errors[] = "password is required.";
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $response["message"] = "Validation errors occurred.";
        $response["data"] = ["errors" => $errors];
        echo json_encode($response);
        exit;
    }

    try {
        // Prepare the SQL statement to fetch user data
        $stmt = $db->prepare('SELECT userId, loginName, passwd, email, firstName, lastName FROM users WHERE loginName = :loginName');
        $stmt->bindParam(':loginName', $loginName);
        $stmt->execute();

        // Fetch user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the password
            if (password_verify($passwd, $user['passwd'])) {
                // Remove password from response for security
                unset($user['passwd']);

                // Update the response for success
                $response["code"] = 0;
                $response["message"] = "Login successful.";
                $response["data"] = $user;

                echo json_encode($response);
            } else {
                // Invalid password
                $response["message"] = "Invalid loginName or password.";
                echo json_encode($response);
            }
        } else {
            // User not found
            $response["message"] = "Invalid loginName or password.";
            echo json_encode($response);
        }
    } catch (PDOException $e) {
        // Handle database errors
        $response["message"] = "Database error: " . $e->getMessage();
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
