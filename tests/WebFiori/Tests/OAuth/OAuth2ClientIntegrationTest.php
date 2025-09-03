<?php
namespace WebFiori\Tests\OAuth;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\MicrosoftProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;
use WebFiori\OAuth\Exceptions\OAuth2Exception;

class OAuth2ClientIntegrationTest extends TestCase {
    private MicrosoftProvider $provider;
    private FileTokenStorage $storage;

    protected function setUp(): void {
        $this->provider = new MicrosoftProvider('test_id', 'test_secret', 'http://localhost/callback');
        $this->storage = new FileTokenStorage();
    }

    public function testExchangeCodeForTokenWithInvalidCode(): void {
        $this->expectException(OAuth2Exception::class);
        
        $client = new OAuth2Client($this->provider, $this->storage);
        
        // This should fail with invalid code
        $client->exchangeCodeForToken('invalid_code', 'test_state');
    }

    public function testRefreshTokenWithInvalidToken(): void {
        $this->expectException(OAuth2Exception::class);
        
        $client = new OAuth2Client($this->provider, $this->storage);
        
        // This should fail with invalid refresh token
        $client->refreshToken('invalid_refresh_token');
    }
}
