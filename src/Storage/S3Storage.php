<?php
namespace App\Storage;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Exception;

class S3Storage implements \App\Contracts\IS3Storage {
    private S3Client $s3;

    public function __construct() {
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => S3_REGION,
            'endpoint' => S3_ENDPOINT,
            'credentials' => [
                'key' => S3_KEY,
                'secret' => S3_SECRET,
            ],
            'use_path_style_endpoint' => true,
        ]);
    }

    public function uploadFile(string $filePath, string $key): string {
        try {
            $result = $this->s3->putObject([
                'Bucket' => S3_BUCKET,
                'Key' => $key,
                'SourceFile' => $filePath,
            ]);
            return $this->getPresignedUrl($key);
        } catch (AwsException $e) {
            throw new Exception("S3 upload failed: " . $e->getMessage());
        }
    }

    public function getPresignedUrl(string $key, int $expires = 3600): string {
        try {
            $cmd = $this->s3->getCommand('GetObject', [
                'Bucket' => S3_BUCKET,
                'Key' => $key,
            ]);
            $request = $this->s3->createPresignedRequest($cmd, "+{$expires} seconds");
            return (string) $request->getUri();
        } catch (AwsException $e) {
            throw new Exception("Failed to generate presigned URL: " . $e->getMessage());
        }
    }

    public function deleteFile(string $key): bool {
        try {
            $this->s3->deleteObject([
                'Bucket' => S3_BUCKET,
                'Key' => $key,
            ]);
            return true;
        } catch (AwsException $e) {
            return false;
        }
    }
}