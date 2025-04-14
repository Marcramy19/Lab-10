
<?php
require 'db.php';
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM tasks");
        echo json_encode($stmt->fetchAll());
        break;
    case 'POST':
        if (!isset($data['title']) || !isset($data['description'])) {
            echo json_encode(["success" => false, "message" => "Missing task details"]);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description) VALUES (?, ?)");
        $stmt->execute([$data['title'], $data['description']]);
        echo json_encode(["success" => true, "message" => "Task added"]);
        break;
    case 'PUT':
        if (!isset($data['id']) || !isset($data['status'])) {
            echo json_encode(["success" => false, "message" => "Missing task ID or status"]);
            exit;
        }
        try
        {
             $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $data['id']]);
            echo json_encode(["success" => true, "message" => "Task updated"]);
        }
        catch(PDOException $e) {
            echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
        }
        break;
    case 'DELETE':
        if (!isset($data['id'])) {
            echo json_encode(["success" => false, "message" => "Missing task ID"]);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(["success" => true, "message" => "Task deleted"]);
        break;
    default:
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
