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
 */
interface TokenStorage {
    /**
     * Delete stored token.
     */
    public function delete(string $key): bool;

    /**
     * Check if token exists.
     */
    public function exists(string $key): bool;

    /**
     * Retrieve stored token.
     */
    public function retrieve(string $key): ?array;

    /**
     * Store token data.
     */
    public function store(string $key, array $token): bool;
}
