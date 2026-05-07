<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config.php';

use App\Services\ImageService;
use App\Repositories\Database;
use App\Storage\S3Storage;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));
$id = isset($pathParts[2]) ? (int)$pathParts[2] : null;

$service = new ImageService(new Database(), new S3Storage());

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $image = $service->getImageById($id);
                if (!$image) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Image not found']);
                    break;
                }
                echo json_encode($image);
            } else {
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
                $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
                $order = isset($_GET['sort']) && in_array($_GET['sort'], ['asc', 'desc']) ? $_GET['sort'] : 'desc';
                $images = $service->getImages($limit, $offset, $order);
                echo json_encode($images);
            }
            break;

        case 'POST':
            if (!isset($_FILES['image'])) {
                http_response_code(400);
                echo json_encode(['error' => 'No file uploaded']);
                break;
            }

            $file = $_FILES['image'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(['error' => 'Upload error']);
                break;
            }

            $tempPath = $file['tmp_name'];
            $image = $service->uploadImage($tempPath);
            http_response_code(201);
            echo json_encode($image);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID required']);
                break;
            }

            $service->deleteImage($id);
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    error_log(date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", 3, LOG_FILE);
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>