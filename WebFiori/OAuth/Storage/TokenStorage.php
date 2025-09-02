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
 * Token storage interface.
 * 
 * Defines the contract for token storage implementations. Implementations
 * can use various backends like files, databases, or memory caches.
 * 
 * @example
 * ```php
 * class DatabaseTokenStorage implements TokenStorage {
 *     public function store(string $key, array $token): bool {
 *         // Store token in database
 *         return true;
 *     }
 *     // ... implement other methods
 * }
 * ```
 */
interface TokenStorage {
    /**
     * Delete stored token.
     * 
     * @param string $key Unique identifier for the token
     * @return bool True if token was deleted or didn't exist, false on failure
     */
    public function delete(string $key): bool;

    /**
     * Check if token exists.
     * 
     * @param string $key Unique identifier for the token
     * @return bool True if token exists, false otherwise
     */
    public function exists(string $key): bool;

    /**
     * Retrieve stored token.
     * 
     * @param string $key Unique identifier for the token
     * @return array<string, mixed>|null Token data or null if not found
     */
    public function retrieve(string $key): ?array;

    /**
     * Store token data.
     * 
     * @param string $key Unique identifier for the token
     * @param array<string, mixed> $token Token data to store
     * @return bool True if token was stored successfully, false on failure
     */
    public function store(string $key, array $token): bool;
}
