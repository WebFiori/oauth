<?php
namespace WebFiori\Tests\OAuth\Providers;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\Providers\MicrosoftProvider;

class MicrosoftProviderTest extends TestCase {
    private MicrosoftProvider $provider;

    protected function setUp(): void {
        $this->provider = new MicrosoftProvider('client_id', 'client_secret', 'http://localhost/callback');
    }

    public function testConstructorWithDefaultTenant(): void {
        $this->assertInstanceOf(MicrosoftProvider::class, $this->provider);
    }

    public function testConstructorWithCustomTenant(): void {
        $provider = new MicrosoftProvider('client_id', 'client_secret', 'http://localhost/callback', 'tenant123');
        $this->assertInstanceOf(MicrosoftProvider::class, $provider);
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

    public function testGetAuthorizationUrlWithDefaultTenant(): void {
        $url = $this->provider->getAuthorizationUrl();
        $this->assertEquals('https://login.microsoftonline.com/common/oauth2/v2.0/authorize', $url);
    }

    public function testGetAuthorizationUrlWithCustomTenant(): void {
        $provider = new MicrosoftProvider('client_id', 'client_secret', 'http://localhost/callback', 'tenant123');
        $url = $provider->getAuthorizationUrl();
        $this->assertEquals('https://login.microsoftonline.com/tenant123/oauth2/v2.0/authorize', $url);
    }

    public function testGetTokenUrlWithDefaultTenant(): void {
        $url = $this->provider->getTokenUrl();
        $this->assertEquals('https://login.microsoftonline.com/common/oauth2/v2.0/token', $url);
    }

    public function testGetTokenUrlWithCustomTenant(): void {
        $provider = new MicrosoftProvider('client_id', 'client_secret', 'http://localhost/callback', 'tenant123');
        $url = $provider->getTokenUrl();
        $this->assertEquals('https://login.microsoftonline.com/tenant123/oauth2/v2.0/token', $url);
    }

    public function testGetUserInfoUrl(): void {
        $url = $this->provider->getUserInfoUrl();
        $this->assertEquals('https://graph.microsoft.com/v1.0/me', $url);
    }

    public function testGetDefaultScopes(): void {
        $scopes = $this->provider->getDefaultScopes();
        $expected = ['openid', 'profile', 'email'];
        $this->assertEquals($expected, $scopes);
    }
}
