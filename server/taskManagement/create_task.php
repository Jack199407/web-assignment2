<?php
// create_task.php: Handles the creation of new tasks

// Include database connection
require_once '../db.php'; 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and validate input
    $taskName = trim($_POST['taskName'] ?? '');
    $priority = intval($_POST['priority'] ?? -1);
    $dueDate = trim($_POST['dueDate'] ?? '');
    $userId = intval($_POST['userId'] ?? 0);

    // Initialize the response format
    $response = [
        "code" => 1,
        "message" => "Task creation failed.",
        "data" => false
    ];

    // Validation rules
    $errors = [];
    if (empty($taskName)) {
        $errors[] = 'taskName is required.';
    } elseif (strlen($taskName) > 100) {
        $errors[] = 'taskName cannot exceed 100 characters.';
    }

    if (!in_array($priority, [0, 1, 2])) {
        $errors[] = 'priority must be 0 (High), 1 (Middle), or 2 (Low).';
    }

    if (empty($dueDate)) {
        $errors[] = 'dueDate is required.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
        $errors[] = 'dueDate must be in YYYY-MM-DD format.';
    }

    if ($userId <= 0) {
        $errors[] = 'Valid userId is required.';
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $response["message"] = implode(" ", $errors);
        echo json_encode($response);
        exit;
    }

    try {
        // Ensure user exists before creating a task
        $userCheckStmt = $db->prepare('SELECT COUNT(*) FROM users WHERE userId = :userId');
        $userCheckStmt->bindParam(':userId', $userId);
        $userCheckStmt->execute();

        if ($userCheckStmt->fetchColumn() == 0) {
            $response["message"] = "User with the provided userId does not exist.";
            echo json_encode($response);
            exit;
        }

        // Prepare the SQL statement to insert the task
        $stmt = $db->prepare('INSERT INTO tasks (taskName, priority, dueDate, taskStatus, userId) 
                              VALUES (:taskName, :priority, :dueDate, 0, :userId)');
        $stmt->bindParam(':taskName', $taskName);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':dueDate', $dueDate);
        $stmt->bindParam(':userId', $userId);

        // Execute the statement
        $stmt->execute();

        // Update response for success
        $response["code"] = 0;
        $response["message"] = "Task created successfully.";
        $response["data"] = true;

        echo json_encode($response);
    } catch (PDOException $e) {
        // Handle database errors
        $response["message"] = 'Database error: ' . $e->getMessage();
        echo json_encode($response);
    }
} else {
    // Handle unsupported request methods
    $response = [
        "code" => 1,
        "message" => "Only POST method is allowed.",
        "data" => false
    ];
    echo json_encode($response);
}
?>
