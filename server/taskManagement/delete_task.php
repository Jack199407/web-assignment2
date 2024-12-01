<?php
/*
Name: Gao Miaomiao 041135845
Created Date: 2024-11-21
delete_task.php: Handles deleting a task
*/

// Include database connection
require_once '../db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and decode raw JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Initialize response format
    $response = [
        "code" => 1,
        "message" => "Task deletion failed.",
        "data" => false
    ];

    // Validate JSON input and taskId
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response["message"] = "Invalid JSON format.";
        echo json_encode($response);
        exit;
    }

    $taskId = intval($input['taskId'] ?? 0);

    if ($taskId <= 0) {
        $response["message"] = "Valid taskId is required.";
        echo json_encode($response);
        exit;
    }

    try {
        // Check if the task exists and fetch its details
        $checkStmt = $db->prepare('SELECT * FROM tasks WHERE taskId = :taskId');
        $checkStmt->bindParam(':taskId', $taskId);
        $checkStmt->execute();
        $task = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$task) {
            $response["message"] = "Task with the provided taskId does not exist.";
            echo json_encode($response);
            exit;
        }

        // Start a transaction
        $db->beginTransaction();

        // Insert a log entry to indicate the task was deleted
        $logStmt = $db->prepare('INSERT INTO taskLogs (taskId, operatorUserId, operationType, changeTime, taskName, priority, dueDate, taskStatus) 
                                 VALUES (:taskId, :operatorUserId, 2, NOW(), :taskName, :priority, :dueDate, :taskStatus)');
        $logStmt->bindParam(':taskId', $taskId);
        $logStmt->bindParam(':operatorUserId', $task['userId']); // Use the userId from the task
        $logStmt->bindParam(':taskName', $task['taskName']);
        $logStmt->bindParam(':priority', $task['priority']);
        $logStmt->bindParam(':dueDate', $task['dueDate']);
        $logStmt->bindParam(':taskStatus', $task['taskStatus']);
        $logStmt->execute();

        // Delete the task from the tasks table
        $deleteStmt = $db->prepare('DELETE FROM tasks WHERE taskId = :taskId');
        $deleteStmt->bindParam(':taskId', $taskId);
        $deleteStmt->execute();

        // Commit the transaction
        $db->commit();

        // Update response for success
        $response["code"] = 0;
        $response["message"] = "Task deleted successfully.";
        $response["data"] = true;

        echo json_encode($response);
    } catch (PDOException $e) {
        // Rollback transaction in case of error
        $db->rollBack();
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
