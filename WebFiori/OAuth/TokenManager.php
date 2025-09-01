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
namespace WebFiori\OAuth;

use WebFiori\OAuth\Storage\TokenStorage;

/**
 * Manages OAuth2 tokens.
 */
class TokenManager {
    private TokenStorage $storage;

    /**
     * Create new token manager.
     */
    public function __construct(TokenStorage $storage) {
        $this->storage = $storage;
    }

    /**
     * Store token data.
     */
    public function store(string $key, array $token): void {
        $this->storage->store($key, $token);
    }

    /**
     * Retrieve token data.
     */
    public function retrieve(string $key): ?array {
        return $this->storage->retrieve($key);
    }

    /**
     * Delete token data.
     */
    public function delete(string $key): void {
        $this->storage->delete($key);
    }
}
