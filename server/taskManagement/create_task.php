<?php
/*
Name: Gao Miaomiao 041135845
Created Date: 2024-11-21
create_task.php: Handles the creation of new tasks
*/

// Include database connection
require_once '../db.php'; 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if JSON decoding failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            "code" => 1,
            "message" => "Invalid JSON format.",
            "data" => false
        ]);
        exit;
    }

    // Read and validate input
    $taskName = trim($input['taskName'] ?? '');
    $priority = intval($input['priority'] ?? -1);
    $dueDate = trim($input['dueDate'] ?? '');
    $userId = intval($input['userId'] ?? 0);
    $taskStatus = intval($input['taskStatus'] ?? -1);

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

    if (!in_array($taskStatus, [0, 1, 2, 3, 4])) {
        $errors[] = 'task status must be 0 (To Do), 1 (In Progress), 2 (Completed), 3 (Paused), or 4 (Cancelled).';
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $response["message"] = "Validation errors occurred.";
        $response["data"] = ["errors" => $errors];
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
                              VALUES (:taskName, :priority, :dueDate, :taskStatus, :userId)');
        $stmt->bindParam(':taskName', $taskName);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':dueDate', $dueDate);
        $stmt->bindParam(':taskStatus', $taskStatus);
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
