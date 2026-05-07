<?php
namespace App\Contracts;

interface IS3Storage {
    public function uploadFile(string $filePath, string $key): string;
    public function deleteFile(string $key): bool;
    public function getPresignedUrl(string $key, int $expires = 3600): string;
}
?>