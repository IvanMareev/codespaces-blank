<?php
namespace App\Queries;

class SqlQueries {
    public const GET_IMAGES = "SELECT * FROM images ORDER BY uploaded_at %s LIMIT ? OFFSET ?";
    public const GET_IMAGE_BY_ID = "SELECT * FROM images WHERE id = ?";
    public const INSERT_IMAGE = "INSERT INTO images (filename, original_name, size, mime_type, s3_url) VALUES (?, ?, ?, ?, ?)";
    public const DELETE_IMAGE = "DELETE FROM images WHERE id = ?";
}
?>