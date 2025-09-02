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
 * 
 * This class provides a high-level interface for token storage operations,
 * abstracting the underlying storage implementation.
 * 
 * @example
 * ```php
 * $storage = new FileTokenStorage();
 * $manager = new TokenManager($storage);
 * $manager->store('access_token', $tokenData);
 * $token = $manager->retrieve('access_token');
 * ```
 */
class TokenManager {
    /** @var TokenStorage Token storage implementation */
    private TokenStorage $storage;

    /**
     * Create new token manager.
     * 
     * @param TokenStorage $storage Token storage implementation to use
     */
    public function __construct(TokenStorage $storage) {
        $this->storage = $storage;
    }

    /**
     * Store token data.
     * 
     * @param string $key Unique identifier for the token
     * @param array<string, mixed> $token Token data to store
     * @return void
     */
    public function store(string $key, array $token): void {
        $this->storage->store($key, $token);
    }

    /**
     * Retrieve token data.
     * 
     * @param string $key Unique identifier for the token
     * @return array<string, mixed>|null Token data or null if not found
     */
    public function retrieve(string $key): ?array {
        return $this->storage->retrieve($key);
    }

    /**
     * Delete token data.
     * 
     * @param string $key Unique identifier for the token to delete
     * @return void
     */
    public function delete(string $key): void {
        $this->storage->delete($key);
    }
}
