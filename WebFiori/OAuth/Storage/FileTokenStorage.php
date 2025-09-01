<?php
/**
 * This file is licensed under MIT License.
 *
 * Copyright (c) 2025 Ibrahim BinAlshikh and Contributors 
 *
 * For more information on the license, please visit:
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 *
 */
namespace WebFiori\OAuth\Storage;

/**
 * File-based token storage implementation.
 */
class FileTokenStorage implements TokenStorage {
    private string $storageDir;

    /**
     * Create new file token storage.
     */
    public function __construct(string $storageDir = null) {
        $this->storageDir = $storageDir ?? sys_get_temp_dir() . '/oauth_tokens';
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0700, true);
        }
    }

    /**
     * Delete stored token.
     */
    public function delete(string $key): bool {
        $file = $this->getFilePath($key);
        return file_exists($file) ? unlink($file) : true;
    }

    /**
     * Check if token exists.
     */
    public function exists(string $key): bool {
        return file_exists($this->getFilePath($key));
    }

    /**
     * Retrieve stored token.
     */
    public function retrieve(string $key): ?array {
        $file = $this->getFilePath($key);
        if (!file_exists($file)) {
            return null;
        }
        $data = file_get_contents($file);
        return $data ? json_decode($data, true) : null;
    }

    /**
     * Store token data.
     */
    public function store(string $key, array $token): bool {
        $file = $this->getFilePath($key);
        return file_put_contents($file, json_encode($token)) !== false;
    }

    /**
     * Get file path for key.
     */
    private function getFilePath(string $key): string {
        return $this->storageDir . '/' . hash('sha256', $key) . '.json';
    }
}
