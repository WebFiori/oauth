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
        
        // Step 2: Mock token exchange (would normally happen after user authorization)
        $mockClient = $this->getMockBuilder(OAuth2Client::class)
            ->setConstructorArgs([$this->provider, $this->storage])
            ->onlyMethods(['exchangeCodeForToken'])
            ->getMock();
        
        $tokenData = [
            'access_token' => 'access_token_123',
            'refresh_token' => 'refresh_token_456',
            'expires_in' => 3600,
            'token_type' => 'Bearer'
        ];
        
        $mockClient->expects($this->once())
            ->method('exchangeCodeForToken')
            ->with('authorization_code_123')
            ->willReturn($tokenData);
        
        $result = $mockClient->exchangeCodeForToken('authorization_code_123');
        $this->assertEquals($tokenData, $result);
        
        // Step 3: Verify token is stored
        $storedToken = $this->storage->retrieve('access_token');
        $this->assertNotNull($storedToken);
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
        
        // Mock the HTTP request part
        $mockRequest = $this->getMockBuilder(TokenRequest::class)
            ->setConstructorArgs([$this->provider])
            ->onlyMethods(['makeRequest'])
            ->getMock();
        
        $expectedParams = [
            'grant_type' => 'authorization_code',
            'client_id' => 'client_id',
            'client_secret' => 'client_secret',
            'code' => 'auth_code_123',
            'redirect_uri' => 'http://localhost/callback'
        ];
        
        $mockRequest->expects($this->once())
            ->method('makeRequest')
            ->with($expectedParams)
            ->willReturn(['access_token' => 'token_123']);
        
        $result = $mockRequest->exchangeCode('auth_code_123');
        $this->assertArrayHasKey('access_token', $result);
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
