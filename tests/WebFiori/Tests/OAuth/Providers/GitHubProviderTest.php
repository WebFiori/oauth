<?php
namespace WebFiori\Tests\OAuth\Providers;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\Providers\GitHubProvider;

class GitHubProviderTest extends TestCase {
    private GitHubProvider $provider;

    protected function setUp(): void {
        $this->provider = new GitHubProvider('client_id', 'client_secret', 'http://localhost/callback');
    }

    public function testGetAuthorizationUrl(): void {
        $url = $this->provider->getAuthorizationUrl();
        $this->assertEquals('https://github.com/login/oauth/authorize', $url);
    }

    public function testGetTokenUrl(): void {
        $url = $this->provider->getTokenUrl();
        $this->assertEquals('https://github.com/login/oauth/access_token', $url);
    }

    public function testGetScopes(): void {
        $scopes = $this->provider->getScopes();
        $this->assertEquals(['user:email', 'read:user'], $scopes);
    }

    public function testGetClientId(): void {
        $this->assertEquals('client_id', $this->provider->getClientId());
    }

    public function testGetClientSecret(): void {
        $this->assertEquals('client_secret', $this->provider->getClientSecret());
    }

    public function testGetRedirectUri(): void {
        $this->assertEquals('http://localhost/callback', $this->provider->getRedirectUri());
    }

    public function testGetDefaultScopes(): void {
        $scopes = $this->provider->getDefaultScopes();
        $this->assertEquals(['user:email', 'read:user'], $scopes);
    }

    public function testGetUserInfoUrl(): void {
        $url = $this->provider->getUserInfoUrl();
        $this->assertEquals('https://api.github.com/user', $url);
    }
}
