<?php
namespace WebFiori\OAuth\Providers;

/**
 * GitHub OAuth2 provider implementation.
 * 
 * Provides OAuth2 authentication for GitHub applications, supporting
 * access to GitHub APIs including repositories, user data, organizations,
 * and other GitHub services.
 * 
 * @example
 * ```php
 * $provider = new GitHubProvider(
 *     'your-github-client-id',
 *     'your-github-client-secret', 
 *     'https://yourapp.com/callback'
 * );
 * 
 * $client = new OAuth2Client($provider);
 * $authUrl = $client->getAuthorizationUrl(['repo', 'user:email']);
 * ```
 * 
 * @see https://docs.github.com/en/developers/apps/building-oauth-apps
 */
class GitHubProvider extends AbstractProvider {
    /**
     * Get GitHub OAuth2 authorization endpoint URL.
     * 
     * The endpoint URL is 'https://github.com/login/oauth/authorize'
     * 
     * @return string GitHub's OAuth2 authorization URL
     */
    public function getAuthorizationUrl(): string {
        return 'https://github.com/login/oauth/authorize';
    }

    /**
     * Get GitHub OAuth2 token endpoint URL.
     * 
     * The endpoint URL is 'https://github.com/login/oauth/access_token'
     * 
     * @return string GitHub's OAuth2 token exchange URL
     */
    public function getTokenUrl(): string {
        return 'https://github.com/login/oauth/access_token';
    }

    /**
     * Get default OAuth2 scopes for GitHub.
     * 
     * Returns basic user information scopes that allow reading
     * user profile data and email addresses.
     * 
     * @see https://docs.github.com/en/apps/oauth-apps/building-oauth-apps/scopes-for-oauth-apps
     * 
     * @return array<string> Default GitHub OAuth2 scopes. Default scopes are:
     * 'user:email' and 'read:user'.
     */
    public function getDefaultScopes(): array {
        return ['user:email', 'read:user'];
    }

    /**
     * Get GitHub user information API endpoint URL.
     * 
     * The URL is https://api.github.com/user.
     * 
     * @return string GitHub API endpoint for retrieving authenticated user data
     */
    public function getUserInfoUrl(): string {
        return 'https://api.github.com/user';
    }
}
