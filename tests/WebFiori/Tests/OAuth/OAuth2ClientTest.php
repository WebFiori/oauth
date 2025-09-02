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
        $this->storage->expects($this->once())
            ->method('store')
            ->with('access_token', $this->isType('array'));

        $client = $this->getMockBuilder(OAuth2Client::class)
            ->setConstructorArgs([$this->provider, $this->storage])
            ->onlyMethods(['exchangeCodeForToken'])
            ->getMock();

        $tokenData = ['access_token' => 'token123', 'expires_in' => 3600];
        $client->expects($this->once())
            ->method('exchangeCodeForToken')
            ->with('auth_code')
            ->willReturn($tokenData);

        $result = $client->exchangeCodeForToken('auth_code');
        $this->assertEquals($tokenData, $result);
    }

    public function testRefreshTokenSuccess(): void {
        $this->storage->expects($this->once())
            ->method('store')
            ->with('access_token', $this->isType('array'));

        $client = $this->getMockBuilder(OAuth2Client::class)
            ->setConstructorArgs([$this->provider, $this->storage])
            ->onlyMethods(['refreshToken'])
            ->getMock();

        $tokenData = ['access_token' => 'new_token123', 'expires_in' => 3600];
        $client->expects($this->once())
            ->method('refreshToken')
            ->with('refresh_token123')
            ->willReturn($tokenData);

        $result = $client->refreshToken('refresh_token123');
        $this->assertEquals($tokenData, $result);
    }
}
