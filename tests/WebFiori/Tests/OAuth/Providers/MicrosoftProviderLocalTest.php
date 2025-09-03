<?php
namespace WebFiori\Tests\OAuth\Providers;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\Providers\MicrosoftProvider;
use WebFiori\OAuth\OAuth2Client;

class MicrosoftProviderLocalTest extends TestCase {
    private array $config;
    private string $configPath;
    private string $samplePath;

    protected function setUp(): void {
        $this->configPath = __DIR__ . '/../../../../../config/local.php';
        $this->samplePath = __DIR__ . '/../../../../../config/local-sample.php';
        
        $this->ensureConfigExists();
        $this->config = require $this->configPath;
        
        if ($this->isUsingDefaultValues()) {
            $this->markTestSkipped('Configuration is using default values from local-sample.php. Please update config/local.php with real Microsoft app credentials.');
        }
    }

    private function ensureConfigExists(): void {
        if (!file_exists($this->configPath)) {
            if (file_exists($this->samplePath)) {
                copy($this->samplePath, $this->configPath);
            } else {
                $this->fail('Neither config/local.php nor config/local-sample.php exists');
            }
        }
    }

    private function isUsingDefaultValues(): bool {
        return $this->config['microsoft']['client_id'] === 'your-client-id-here' &&
               $this->config['microsoft']['client_secret'] === 'your-client-secret-here';
    }

    public function testProviderWithRealConfig(): void {
        $provider = new MicrosoftProvider(
            $this->config['microsoft']['client_id'],
            $this->config['microsoft']['client_secret'],
            $this->config['microsoft']['redirect_uri'],
            $this->config['microsoft']['tenant_id']
        );

        $this->assertInstanceOf(MicrosoftProvider::class, $provider);
        $this->assertEquals($this->config['microsoft']['client_id'], $provider->getClientId());
        $this->assertEquals($this->config['microsoft']['client_secret'], $provider->getClientSecret());
        $this->assertEquals($this->config['microsoft']['redirect_uri'], $provider->getRedirectUri());
    }

    public function testAuthorizationUrlWithRealConfig(): void {
        $provider = new MicrosoftProvider(
            $this->config['microsoft']['client_id'],
            $this->config['microsoft']['client_secret'],
            $this->config['microsoft']['redirect_uri'],
            $this->config['microsoft']['tenant_id']
        );

        $client = new OAuth2Client($provider);
        $authUrl = $client->getAuthorizationUrl(['openid', 'profile', 'email']);

        $this->assertStringContainsString('login.microsoftonline.com', $authUrl);
        $this->assertStringContainsString($this->config['microsoft']['tenant_id'], $authUrl);
        $this->assertStringContainsString('client_id=' . urlencode($this->config['microsoft']['client_id']), $authUrl);
        $this->assertStringContainsString('redirect_uri=' . urlencode($this->config['microsoft']['redirect_uri']), $authUrl);
    }

    public function testRefreshTokenAndGraphAPI(): void {
        if (!isset($this->config['microsoft']['refresh_token']) || empty($this->config['microsoft']['refresh_token'])) {
            $this->markTestSkipped('No refresh token configured in config/local.php. Add refresh_token key with valid token.');
        }

        $provider = new MicrosoftProvider(
            $this->config['microsoft']['client_id'],
            $this->config['microsoft']['client_secret'],
            $this->config['microsoft']['redirect_uri'],
            $this->config['microsoft']['tenant_id']
        );

        $client = new OAuth2Client($provider);
        
        try {
            $tokens = $client->refreshToken($this->config['microsoft']['refresh_token']);
        } catch (Exception $e) {
            $this->markTestSkipped('Refresh token failed: ' . $e->getMessage() . '. Token may be expired.');
        }
        
        $this->assertArrayHasKey('access_token', $tokens);
        $this->assertNotEmpty($tokens['access_token']);
        
        // Test Microsoft Graph API call
        $ch = curl_init('https://graph.microsoft.com/v1.0/me');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $tokens['access_token']
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $userData = json_decode($response, true);
            $this->assertIsArray($userData);
            $this->assertArrayHasKey('userPrincipalName', $userData, 'Microsoft Graph should return user principal name');
        } else {
            $this->markTestIncomplete('Token refresh successful but Graph API call failed. This may be due to insufficient scopes.');
        }
    }
}
