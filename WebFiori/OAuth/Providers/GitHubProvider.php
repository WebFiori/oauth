<?php
namespace WebFiori\OAuth\Providers;

/**
 * GitHub OAuth2 provider implementation.
 */
class GitHubProvider extends AbstractProvider {
    /**
     * Create new GitHub OAuth2 provider.
     * 
     * @param string $clientId GitHub OAuth app client ID
     * @param string $clientSecret GitHub OAuth app client secret
     * @param string $redirectUri Callback URL registered with GitHub
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUri) {
        parent::__construct($clientId, $clientSecret, $redirectUri);
    }

    public function getAuthorizationUrl(): string {
        return 'https://github.com/login/oauth/authorize';
    }

    public function getTokenUrl(): string {
        return 'https://github.com/login/oauth/access_token';
    }

    public function getScopes(): array {
        return ['user:email', 'read:user'];
    }

    public function getDefaultScopes(): array {
        return ['user:email', 'read:user'];
    }

    public function getUserInfoUrl(): string {
        return 'https://api.github.com/user';
    }
}
