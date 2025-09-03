<?php
namespace WebFiori\Tests\OAuth\Providers;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\Providers\GoogleProvider;

class GoogleProviderTest extends TestCase {
    private GoogleProvider $provider;

    protected function setUp(): void {
        $this->provider = new GoogleProvider('test-client-id', 'test-client-secret', 'http://localhost/callback');
    }

    public function testGetAuthorizationUrl(): void {
        $this->assertEquals('https://accounts.google.com/o/oauth2/v2/auth', $this->provider->getAuthorizationUrl());
    }

    public function testGetTokenUrl(): void {
        $this->assertEquals('https://oauth2.googleapis.com/token', $this->provider->getTokenUrl());
    }

    public function testGetDefaultScopes(): void {
        $scopes = $this->provider->getDefaultScopes();
        $this->assertEquals(['openid', 'email', 'profile'], $scopes);
    }

    public function testGetRedirectUri(): void {
        $this->assertEquals('http://localhost/callback', $this->provider->getRedirectUri());
    }

    public function testGetUserInfoUrl(): void {
        $url = $this->provider->getUserInfoUrl();
        $this->assertEquals('https://www.googleapis.com/oauth2/v2/userinfo', $url);
    }
}
