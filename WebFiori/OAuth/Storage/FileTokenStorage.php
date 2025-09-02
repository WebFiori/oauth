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
 * 
 * Stores OAuth2 tokens as JSON files in a specified directory.
 * Token keys are hashed for security and file names are generated
 * using SHA-256 to prevent directory traversal attacks.
 * 
 * @example
 * ```php
 * $storage = new FileTokenStorage('/secure/token/directory');
 * $storage->store('user_123_token', $tokenData);
 * $token = $storage->retrieve('user_123_token');
 * ```
 */
class FileTokenStorage implements TokenStorage {
    /** @var string Directory path for storing token files */
    private string $storageDir;

    /**
     * Create new file token storage.
     * 
     * @param string|null $storageDir Directory to store token files (defaults to system temp directory)
     */
    public function __construct(string $storageDir = null) {
        $this->storageDir = $storageDir ?? sys_get_temp_dir() . '/oauth_tokens';
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0700, true);
        }
    }

    /**
     * Delete stored token.
     * 
     * @param string $key Unique identifier for the token
     * @return bool True if token was deleted or didn't exist, false on failure
     */
    public function delete(string $key): bool {
        $file = $this->getFilePath($key);
        return file_exists($file) ? unlink($file) : true;
    }

    /**
     * Check if token exists.
     * 
     * @param string $key Unique identifier for the token
     * @return bool True if token file exists, false otherwise
     */
    public function exists(string $key): bool {
        return file_exists($this->getFilePath($key));
    }

    /**
     * Retrieve stored token.
     * 
     * @param string $key Unique identifier for the token
     * @return array<string, mixed>|null Token data or null if not found or invalid JSON
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
     * 
     * @param string $key Unique identifier for the token
     * @param array<string, mixed> $token Token data to store as JSON
     * @return bool True if token was stored successfully, false on failure
     */
    public function store(string $key, array $token): bool {
        $file = $this->getFilePath($key);
        return file_put_contents($file, json_encode($token)) !== false;
    }

    /**
     * Get file path for key.
     * 
     * Generates a secure file path by hashing the key to prevent
     * directory traversal attacks and ensure consistent naming.
     * 
     * @param string $key Token identifier to hash
     * @return string Full file path for the token
     */
    private function getFilePath(string $key): string {
        return $this->storageDir . '/' . hash('sha256', $key) . '.json';
    }
}
