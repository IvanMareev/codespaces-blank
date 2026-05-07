<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

use App\ImageService;

$service = new ImageService();
$service->updatePresignedUrls();

echo "Presigned URLs updated successfully.\n";
?>