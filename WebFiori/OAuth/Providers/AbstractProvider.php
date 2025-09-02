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
 * 
 * Base class for OAuth2 providers that handles common configuration
 * like client credentials and redirect URI. Concrete providers should
 * extend this class and implement the abstract methods.
 * 
 * @example
 * ```php
 * class MyProvider extends AbstractProvider {
 *     public function getAuthorizationUrl(): string {
 *         return 'https://api.example.com/oauth/authorize';
 *     }
 *     // ... implement other abstract methods
 * }
 * ```
 */
abstract class AbstractProvider implements Provider {
    /** @var string OAuth2 client identifier */
    protected string $clientId;
    
    /** @var string OAuth2 client secret */
    protected string $clientSecret;
    
    /** @var string OAuth2 redirect URI */
    protected string $redirectUri;

    /**
     * Create new provider.
     * 
     * @param string $clientId OAuth2 client identifier
     * @param string $clientSecret OAuth2 client secret
     * @param string $redirectUri OAuth2 redirect URI for callbacks
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUri) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * Get the client ID.
     * 
     * @return string OAuth2 client identifier
     */
    public function getClientId(): string {
        return $this->clientId;
    }

    /**
     * Get the client secret.
     * 
     * @return string OAuth2 client secret
     */
    public function getClientSecret(): string {
        return $this->clientSecret;
    }

    /**
     * Get the redirect URI.
     * 
     * @return string OAuth2 redirect URI
     */
    public function getRedirectUri(): string {
        return $this->redirectUri;
    }
}
