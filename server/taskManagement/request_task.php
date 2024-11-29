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
    $taskStatus = $input['taskStatus'] ?? null; // Array of taskStatus values

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

        // Optional filters: Priority
        if (!empty($priority) && is_array($priority)) {
            $priorityPlaceholders = [];
            foreach ($priority as $index => $value) {
                $paramKey = ":priority_$index";
                $priorityPlaceholders[] = $paramKey;
                $params[$paramKey] = $value;
            }
            $sql .= " AND priority IN (" . implode(',', $priorityPlaceholders) . ")";
        }

        // Optional filters: Due Date
        if (!empty($dueDate)) {
            $sql .= " AND dueDate = :dueDate";
            $params[':dueDate'] = $dueDate;
        }

        // Optional filters: Task Status
        if (!empty($taskStatus) && is_array($taskStatus)) {
            $taskStatusPlaceholders = [];
            foreach ($taskStatus as $index => $value) {
                $paramKey = ":taskStatus_$index";
                $taskStatusPlaceholders[] = $paramKey;
                $params[$paramKey] = $value;
            }
            $sql .= " AND taskStatus IN (" . implode(',', $taskStatusPlaceholders) . ")";
        }

        // Prepare and execute the query
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Translate priority and taskStatus to text
        $priorityMapping = [0 => 'High', 1 => 'Middle', 2 => 'Low'];
        $statusMapping = [0 => 'ToDo', 1 => 'InProgress', 2 => 'Completed', 3 => 'Paused', 4 => 'Cancelled'];

        foreach ($tasks as &$task) {
            $task['priority'] = $priorityMapping[$task['priority']] ?? 'Unknown';
            $task['taskStatus'] = $statusMapping[$task['taskStatus']] ?? 'Unknown';
        }

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
