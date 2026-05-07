<?php
namespace App\Contracts;

interface IImageService {
    public function uploadImage(string $filePath): array;
    public function deleteImage(int $id): bool;
    public function getImages(int $limit = 20, int $offset = 0, string $order = 'desc'): array;
    public function getImageById(int $id): ?array;
}
?>