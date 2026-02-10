<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Application\Mail;

header("Content-Type: application/json");

$dsn = "pgsql:host=" . getenv('DB_PROD_HOST') . ";dbname=" . getenv('DB_PROD_NAME');
$pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mail = new Mail($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Extract ID from URL like /api/mail/1
$id = null;
if (preg_match('#/api/mail/(\d+)#', $path, $matches)) {
    $id = (int)$matches[1];
}

try {
    switch ($method) {

        case 'GET':
            if ($id) {
                $result = $mail->getMailById($id);
                if ($result) {
                    echo json_encode($result);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Mail not found"]);
                }
            } else {
                echo json_encode($mail->getAllMail());
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['subject'], $data['body'])) {
                http_response_code(400);
                echo json_encode(["error" => "Bad request"]);
                exit;
            }

            $newId = $mail->createMail($data['subject'], $data['body']);
            echo json_encode(["id" => $newId]);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "ID required"]);
                exit;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            $mail->updateMail($id, $data['subject'], $data['body']);
            echo json_encode(["status" => "updated"]);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "ID required"]);
                exit;
            }

            $mail->deleteMail($id);
            echo json_encode(["status" => "deleted"]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
