<?php
// update_task.php: Handles the update of an existing task

// Include database connection
require_once '../db.php'; 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and validate input
    $taskId = intval($_POST['taskId'] ?? 0);
    $taskName = trim($_POST['taskName'] ?? '');
    $priority = intval($_POST['priority'] ?? -1);
    $dueDate = trim($_POST['dueDate'] ?? '');
    $taskStatus = intval($_POST['taskStatus'] ?? -1);

    // Initialize response format
    $response = [
        "code" => 1,
        "message" => "Task update failed.",
        "data" => false
    ];

    // Validation rules
    if ($taskId <= 0) {
        $response["message"] = "Valid taskId is required.";
        echo json_encode($response);
        exit;
    }

    if (empty($taskName)) {
        $response["message"] = "taskName is required.";
        echo json_encode($response);
        exit;
    } elseif (strlen($taskName) > 100) {
        $response["message"] = "taskName cannot exceed 100 characters.";
        echo json_encode($response);
        exit;
    }

    if (!in_array($priority, [0, 1, 2])) {
        $response["message"] = "priority must be 0 (High), 1 (Middle), or 2 (Low).";
        echo json_encode($response);
        exit;
    }

    if (empty($dueDate)) {
        $response["message"] = "dueDate is required.";
        echo json_encode($response);
        exit;
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
        $response["message"] = "dueDate must be in YYYY-MM-DD format.";
        echo json_encode($response);
        exit;
    }

    if (!in_array($taskStatus, [0, 1, 2, 3, 4])) {
        $response["message"] = "taskStatus must be 0 (ToDo), 1 (InProgress), 2 (Completed), 3 (Paused), or 4 (Cancelled).";
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
