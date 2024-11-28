<?php 
// request_task.php: Handles retrieving tasks based on filters

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
            "data" => []
        ]);
        exit;
    }

    // Read input
    $userId = intval($input['userId'] ?? 0);
    $priority = $input['priority'] ?? null; // Array of priorities
    $dueDate = trim($input['dueDate'] ?? '');
    $taskStatus = isset($input['taskStatus']) ? intval($input['taskStatus']) : null;

    // Initialize response format
    $response = [
        "code" => 1,
        "message" => "Failed to retrieve tasks.",
        "data" => []
    ];

    // Validate userId
    $errors = [];
    if ($userId <= 0) {
        $errors[] = "Valid userId is required.";
    }

    if (!empty($dueDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
        $errors[] = "dueDate must be in YYYY-MM-DD format.";
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $response["message"] = "Validation errors occurred.";
        $response["data"] = ["errors" => $errors];
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

        if (!empty($dueDate)) {
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
