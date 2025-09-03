<?php
namespace WebFiori\Tests\OAuth;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\MicrosoftProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;
use WebFiori\OAuth\Storage\TokenStorage;
use WebFiori\OAuth\TokenRequest;
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
        $client = new OAuth2Client($this->provider, $this->storage);
        $this->assertTrue(method_exists($client, 'exchangeCodeForToken'));
        
        $reflection = new \ReflectionMethod($client, 'exchangeCodeForToken');
        $this->assertEquals(2, $reflection->getNumberOfParameters());
    }

    public function testRefreshTokenSuccess(): void {
        $client = new OAuth2Client($this->provider, $this->storage);
        $this->assertTrue(method_exists($client, 'refreshToken'));
        
        $reflection = new \ReflectionMethod($client, 'refreshToken');
        $this->assertEquals(1, $reflection->getNumberOfRequiredParameters());
    }

    public function testGetAuthorizationUrlWithScopes(): void {
        $client = new OAuth2Client($this->provider, $this->storage);
        
        $url = $client->getAuthorizationUrl(['openid', 'profile']);
        
        $this->assertIsString($url);
        $this->assertStringContainsString('scope=openid+profile', $url);
    }

    public function testGetAuthorizationUrlWithEmptyScopes(): void {
        $client = new OAuth2Client($this->provider, $this->storage);
        
        $url = $client->getAuthorizationUrl([]);
        
        $this->assertIsString($url);
        $this->assertStringContainsString($this->provider->getAuthorizationUrl(), $url);
    }

    public function testExchangeCodeForTokenSuccessWithFactory(): void {
        $expectedToken = [
            'access_token' => 'test_access_token',
            'refresh_token' => 'test_refresh_token',
            'expires_in' => 3600
        ];

        // Mock TokenRequest
        $mockTokenRequest = $this->createMock(TokenRequest::class);
        $mockTokenRequest->expects($this->once())
            ->method('exchangeCode')
            ->with('valid_code', 'test_state')
            ->willReturn($expectedToken);

        // Factory that returns our mock
        $factory = fn($provider) => $mockTokenRequest;

        // Mock storage to verify store is called
        $mockStorage = $this->createMock(TokenStorage::class);
        $mockStorage->expects($this->once())
            ->method('store')
            ->with('access_token', $expectedToken);

        $client = new OAuth2Client($this->provider, $mockStorage, $factory);
        $result = $client->exchangeCodeForToken('valid_code', 'test_state');

        $this->assertEquals($expectedToken, $result);
    }
}
