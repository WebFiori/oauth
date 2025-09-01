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
namespace WebFiori\OAuth\Providers;

/**
 * Abstract OAuth2 provider implementation.
 */
abstract class AbstractProvider implements Provider {
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;

    /**
     * Create new provider.
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUri) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * Get the client ID.
     */
    public function getClientId(): string {
        return $this->clientId;
    }

    /**
     * Get the client secret.
     */
    public function getClientSecret(): string {
        return $this->clientSecret;
    }

    /**
     * Get the redirect URI.
     */
    public function getRedirectUri(): string {
        return $this->redirectUri;
    }
}
