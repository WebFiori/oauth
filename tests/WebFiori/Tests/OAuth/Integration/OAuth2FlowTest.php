<?php
namespace WebFiori\Tests\OAuth\Integration;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\MicrosoftProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;
use WebFiori\OAuth\TokenRequest;
use WebFiori\OAuth\AuthorizationRequest;

class OAuth2FlowTest extends TestCase {
    private MicrosoftProvider $provider;
    private FileTokenStorage $storage;
    private string $tempDir;

    protected function setUp(): void {
        $this->tempDir = sys_get_temp_dir() . '/oauth_integration_test_' . uniqid();
        $this->provider = new MicrosoftProvider('client_id', 'client_secret', 'http://localhost/callback');
        $this->storage = new FileTokenStorage($this->tempDir);
    }

    protected function tearDown(): void {
        // Clean up test files
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($this->tempDir);
        }
    }

    public function testCompleteOAuth2Flow(): void {
        $client = new OAuth2Client($this->provider, $this->storage);
        
        // Step 1: Get authorization URL
        $authUrl = $client->getAuthorizationUrl(['openid', 'profile']);
        $this->assertStringContainsString('login.microsoftonline.com', $authUrl);
        $this->assertStringContainsString('scope=openid+profile', $authUrl);
        
        // Step 2: Simulate token storage (since we can't mock HTTP easily)
        $tokenData = [
            'access_token' => 'access_token_123',
            'refresh_token' => 'refresh_token_456',
            'expires_in' => 3600,
            'token_type' => 'Bearer'
        ];
        
        // Manually store token to test storage integration
        $this->storage->store('access_token', $tokenData);
        
        // Step 3: Verify token is stored
        $storedToken = $this->storage->retrieve('access_token');
        $this->assertNotNull($storedToken);
        $this->assertEquals($tokenData, $storedToken);
    }

    public function testAuthorizationRequestIntegration(): void {
        $authRequest = new AuthorizationRequest($this->provider);
        $url = $authRequest->buildUrl(['read', 'write']);
        
        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $params);
        
        $this->assertEquals('code', $params['response_type']);
        $this->assertEquals('client_id', $params['client_id']);
        $this->assertEquals('http://localhost/callback', $params['redirect_uri']);
        $this->assertEquals('read write', $params['scope']);
        $this->assertArrayHasKey('state', $params);
    }

    public function testTokenRequestIntegration(): void {
        $tokenRequest = new TokenRequest($this->provider);
        
        // Test that the TokenRequest can be instantiated and has the right methods
        $this->assertTrue(method_exists($tokenRequest, 'exchangeCode'));
        $this->assertTrue(method_exists($tokenRequest, 'refresh'));
        
        // Test parameter validation by checking the provider integration
        $this->assertEquals('client_id', $this->provider->getClientId());
        $this->assertEquals('client_secret', $this->provider->getClientSecret());
        $this->assertEquals('http://localhost/callback', $this->provider->getRedirectUri());
    }

    public function testStorageIntegration(): void {
        $client = new OAuth2Client($this->provider, $this->storage);
        
        // Test that storage is properly integrated
        $tokenData = ['access_token' => 'test_token', 'expires_in' => 3600];
        $this->storage->store('test_key', $tokenData);
        
        $retrieved = $this->storage->retrieve('test_key');
        $this->assertEquals($tokenData, $retrieved);
        
        // Test deletion
        $this->storage->delete('test_key');
        $this->assertNull($this->storage->retrieve('test_key'));
    }
}
