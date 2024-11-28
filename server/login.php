<?php
// login.php: Handles user authentication

// Include database connection
require_once 'db.php'; 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and validate input
    $loginName = trim($_POST['loginName'] ?? '');
    $passwd = trim($_POST['passwd'] ?? '');

    // Initialize the response format
    $response = [
        "code" => 1,
        "message" => "Login failed.",
        "data" => null
    ];

    // Basic validation
    if (empty($loginName) || empty($passwd)) {
        $response["message"] = "loginName and password are required.";
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
