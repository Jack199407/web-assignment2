<?php
// login.php: Handles user authentication

// Include database connection
require_once 'db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and validate input
    $loginName = trim($_POST['loginName'] ?? '');
    $passwd = trim($_POST['passwd'] ?? '');

    // Basic validation
    if (empty($loginName) || empty($passwd)) {
        http_response_code(400);
        echo json_encode(['error' => 'loginName and passwd are required.']);
        exit;
    }

    try {
        // Prepare the SQL statement to fetch user data
        $stmt = $db->prepare('SELECT userId, passwd FROM users WHERE loginName = :loginName');
        $stmt->bindParam(':loginName', $loginName);
        $stmt->execute();

        // Fetch user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the password
            if (password_verify($passwd, $user['passwd'])) {
                // Generate a session or token (this example assumes a simple session)
                session_start();
                $_SESSION['userId'] = $user['userId'];

                // Send success response
                http_response_code(200); // HTTP 200 OK
                echo json_encode([
                    'message' => 'Login successful.',
                    'userId' => $user['userId']
                ]);
            } else {
                // Invalid password
                http_response_code(401); // HTTP 401 Unauthorized
                echo json_encode(['error' => 'Invalid loginName or password.']);
            }
        } else {
            // User not found
            http_response_code(401); // HTTP 401 Unauthorized
            echo json_encode(['error' => 'Invalid loginName or password.']);
        }
    } catch (PDOException $e) {
        // Handle database errors
        http_response_code(500); // HTTP 500 Internal Server Error
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Handle unsupported request methods
    http_response_code(405); // HTTP 405 Method Not Allowed
    echo json_encode(['error' => 'Only POST method is allowed.']);
}
?>
