<?php
// delete_task.php: Handles deleting a task

// Include database connection
require_once '../db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read input
    $taskId = intval($_POST['taskId'] ?? 0);

    // Initialize response format
    $response = [
        "code" => 1,
        "message" => "Task deletion failed.",
        "data" => false
    ];

    // Validate taskId
    if ($taskId <= 0) {
        $response["message"] = "Valid taskId is required.";
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

        // Delete the task
        $deleteStmt = $db->prepare('DELETE FROM tasks WHERE taskId = :taskId');
        $deleteStmt->bindParam(':taskId', $taskId);

        // Execute the statement
        $deleteStmt->execute();

        // Update response for success
        $response["code"] = 0;
        $response["message"] = "Task deleted successfully.";
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
