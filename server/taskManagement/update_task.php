<?php
// update_task.php: Handles the update of an existing task

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
    $taskId = intval($input['taskId'] ?? 0);
    $taskName = trim($input['taskName'] ?? '');
    $priority = intval($input['priority'] ?? -1);
    $dueDate = trim($input['dueDate'] ?? '');
    $taskStatus = intval($input['taskStatus'] ?? -1);

    // Initialize response format
    $response = [
        "code" => 1,
        "message" => "Task update failed.",
        "data" => false
    ];

    // Validation rules
    $errors = [];
    if ($taskId <= 0) {
        $errors[] = "Valid taskId is required.";
    }

    if (empty($taskName)) {
        $errors[] = "taskName is required.";
    } elseif (strlen($taskName) > 100) {
        $errors[] = "taskName cannot exceed 100 characters.";
    }

    if (!in_array($priority, [0, 1, 2])) {
        $errors[] = "priority must be 0 (High), 1 (Middle), or 2 (Low).";
    }

    if (empty($dueDate)) {
        $errors[] = "dueDate is required.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
        $errors[] = "dueDate must be in YYYY-MM-DD format.";
    }

    if (!in_array($taskStatus, [0, 1, 2, 3, 4])) {
        $errors[] = "taskStatus must be 0 (ToDo), 1 (InProgress), 2 (Completed), 3 (Paused), or 4 (Cancelled).";
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $response["message"] = "Validation errors occurred.";
        $response["data"] = ["errors" => $errors];
        echo json_encode($response);
        exit;
    }

    try {
        // Check if the task exists
        $checkStmt = $db->prepare('SELECT COUNT(*) FROM tasks WHERE taskId = :taskId');
        $checkStmt->bindParam(':taskId', $taskId);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() == 0) {
            $response["message"] = "Task with the provided taskId does not exist.";
            echo json_encode($response);
            exit;
        }

        // Update the task
        $stmt = $db->prepare('UPDATE tasks SET taskName = :taskName, priority = :priority, dueDate = :dueDate, taskStatus = :taskStatus WHERE taskId = :taskId');
        $stmt->bindParam(':taskName', $taskName);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':dueDate', $dueDate);
        $stmt->bindParam(':taskStatus', $taskStatus);
        $stmt->bindParam(':taskId', $taskId);

        // Execute the statement
        $stmt->execute();

        // Update response for success
        $response["code"] = 0;
        $response["message"] = "Task updated successfully.";
        $response["data"] = true;

        echo json_encode($response);
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
        "data" => false
    ];
    echo json_encode($response);
}
?>
