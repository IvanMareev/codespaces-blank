<?php
namespace App\Services;

use App\Contracts\IDatabase;
use App\Contracts\IS3Storage;
use App\Queries\SqlQueries;
use Exception;

class ImageService implements \App\Contracts\IImageService {
    private \App\Contracts\IDatabase $db;
    private \App\Contracts\IS3Storage $s3;

    public function __construct(IDatabase $db, IS3Storage $s3) {
        $this->db = $db;
        $this->s3 = $s3;
    }

    public function uploadImage(string $filePath): array {
        $this->validateFile($filePath);

        $filename = $this->generateUniqueFilename($filePath);

        $s3Url = $this->s3->uploadFile($filePath, $filename);

        $originalName = basename($filePath);
        $size = filesize($filePath);
        $mimeType = mime_content_type($filePath);

        $sql = SqlQueries::INSERT_IMAGE;
        $this->db->query($sql, [$filename, $originalName, $size, $mimeType, $s3Url]);

        $id = $this->db->getLastInsertId();

        return [
            'id' => $id,
            'filename' => $filename,
            'original_name' => $originalName,
            'size' => $size,
            'mime_type' => $mimeType,
            'uploaded_at' => date('Y-m-d H:i:s'),
            's3_url' => $s3Url,
        ];
    }

    public function deleteImage(int $id): bool {
        $image = $this->getImageById($id);
        if (!$image) {
            throw new Exception("Image not found");
        }

        $this->s3->deleteFile($image['filename']);

        $sql = SqlQueries::DELETE_IMAGE;
        $this->db->query($sql, [$id]);

        return true;
    }

    public function getImages(int $limit = 20, int $offset = 0, string $order = 'desc'): array {
        $orderBy = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        $sql = sprintf(SqlQueries::GET_IMAGES, $orderBy);
        $result = $this->db->query($sql, [$limit, $offset]);

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $row['s3_url'] = $this->s3->getPresignedUrl($row['filename']);
            $images[] = $row;
        }

        return $images;
    }

    public function getImageById(int $id): ?array {
        $result = $this->db->query(SqlQueries::GET_IMAGE_BY_ID, [$id]);
        $image = $result->fetch_assoc();
        if ($image) {
            $image['s3_url'] = $this->s3->getPresignedUrl($image['filename']);
        }
        return $image;
    }

    private function validateFile(string $filePath): void {
        if (!file_exists($filePath)) {
            throw new Exception("File does not exist");
        }

        $size = filesize($filePath);
        if ($size > MAX_FILE_SIZE) {
            throw new Exception("File size exceeds limit");
        }

        $mimeType = mime_content_type($filePath);
        if (!in_array($mimeType, ALLOWED_MIME_TYPES)) {
            throw new Exception("Invalid file type");
        }
    }

    private function generateUniqueFilename(string $filePath): string {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        return uniqid('img_', true) . '.' . $extension;
    }

    public function updatePresignedUrls(): void {
        $sql = "SELECT id, filename FROM images";
        $result = $this->db->query($sql);

        while ($row = $result->fetch_assoc()) {
            $presignedUrl = $this->s3->getPresignedUrl($row['filename']);
            $updateSql = "UPDATE images SET s3_url = ? WHERE id = ?";
            $this->db->query($updateSql, [$presignedUrl, $row['id']]);
        }
    }
}