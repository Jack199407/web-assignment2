<?php
// request_task.php: Handles retrieving tasks based on filters

// Include database connection
require_once '../db.php'; 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read input
    $userId = intval($_POST['userId'] ?? 0);
    $priority = isset($_POST['priority']) ? $_POST['priority'] : null; // Array of priorities
    $dueDate = trim($_POST['dueDate'] ?? '');
    $taskStatus = isset($_POST['taskStatus']) ? intval($_POST['taskStatus']) : null;

    // Initialize response format
    $response = [
        "code" => 1,
        "message" => "Failed to retrieve tasks.",
        "data" => []
    ];

    // Validate userId
    if ($userId <= 0) {
        $response["message"] = "Valid userId is required.";
        echo json_encode($response);
        exit;
    }

    try {
        // Base SQL query
        $sql = 'SELECT taskId, taskName, priority, dueDate, taskStatus, userId FROM tasks WHERE userId = :userId';
        $params = [':userId' => $userId];

        // Optional filters
        if (!empty($priority) && is_array($priority)) {
            $priorityPlaceholders = implode(',', array_fill(0, count($priority), '?'));
            $sql .= " AND priority IN ($priorityPlaceholders)";
            $params = array_merge($params, $priority);
        }

        if (!empty($dueDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
            $sql .= " AND dueDate = :dueDate";
            $params[':dueDate'] = $dueDate;
        }

        if (!is_null($taskStatus)) {
            $sql .= " AND taskStatus = :taskStatus";
            $params[':taskStatus'] = $taskStatus;
        }

        // Prepare and execute the query
        $stmt = $db->prepare($sql);

        // Bind parameters
        $index = 1;
        foreach ($params as $key => $value) {
            $stmt->bindValue(is_int($key) ? $index++ : $key, $value);
        }

        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Update response for success
        $response["code"] = 0;
        $response["message"] = "Tasks retrieved successfully.";
        $response["data"] = $tasks;

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
        "data" => []
    ];
    echo json_encode($response);
}
?>
