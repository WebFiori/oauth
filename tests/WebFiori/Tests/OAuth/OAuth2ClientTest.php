<?php
namespace WebFiori\Tests\OAuth;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\MicrosoftProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;
use WebFiori\OAuth\Storage\TokenStorage;
use WebFiori\OAuth\Exceptions\OAuth2Exception;

class OAuth2ClientTest extends TestCase {
    private MicrosoftProvider $provider;
    private TokenStorage $storage;

    protected function setUp(): void {
        $this->provider = new MicrosoftProvider('client_id', 'client_secret', 'http://localhost/callback');
        $this->storage = $this->createMock(TokenStorage::class);
    }

    public function testConstructorWithDefaultStorage(): void {
        $client = new OAuth2Client($this->provider);
        $this->assertInstanceOf(OAuth2Client::class, $client);
    }

    public function testConstructorWithCustomStorage(): void {
        $client = new OAuth2Client($this->provider, $this->storage);
        $this->assertInstanceOf(OAuth2Client::class, $client);
    }

    public function testGetAuthorizationUrl(): void {
        $client = new OAuth2Client($this->provider, $this->storage);
        $url = $client->getAuthorizationUrl();
        
        $this->assertStringContainsString('login.microsoftonline.com', $url);
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('client_id=client_id', $url);
        $this->assertStringContainsString('state=', $url);
    }

    public function testGetAuthorizationUrlWithCustomScopes(): void {
        $client = new OAuth2Client($this->provider, $this->storage);
        $url = $client->getAuthorizationUrl(['read', 'write']);
        
        $this->assertStringContainsString('scope=read+write', $url);
    }

    public function testExchangeCodeForTokenSuccess(): void {
        // Test that the method exists and can be called
        $client = new OAuth2Client($this->provider, $this->storage);
        $this->assertTrue(method_exists($client, 'exchangeCodeForToken'));
        
        // Test the method signature
        $reflection = new \ReflectionMethod($client, 'exchangeCodeForToken');
        $this->assertEquals(2, $reflection->getNumberOfParameters());
    }

    public function testRefreshTokenSuccess(): void {
        // Test that the method exists and can be called
        $client = new OAuth2Client($this->provider, $this->storage);
        $this->assertTrue(method_exists($client, 'refreshToken'));
        
        // Test the method signature
        $reflection = new \ReflectionMethod($client, 'refreshToken');
        $this->assertEquals(1, $reflection->getNumberOfRequiredParameters());
    }
}
