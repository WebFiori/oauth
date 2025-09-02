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
        $sampleConfig = require $this->samplePath;
        
        return $this->config['microsoft']['client_id'] === $sampleConfig['microsoft']['client_id'] ||
               $this->config['microsoft']['client_secret'] === $sampleConfig['microsoft']['client_secret'] ||
               str_contains($this->config['microsoft']['client_id'], 'your-client-id') ||
               str_contains($this->config['microsoft']['client_secret'], 'your-client-secret');
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

    public function testTokenEndpointAccessibility(): void {
        $provider = new MicrosoftProvider(
            $this->config['microsoft']['client_id'],
            $this->config['microsoft']['client_secret'],
            $this->config['microsoft']['redirect_uri'],
            $this->config['microsoft']['tenant_id']
        );

        $tokenUrl = $provider->getTokenUrl();
        
        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertNotEquals(0, $httpCode, 'Token endpoint should be accessible');
        $this->assertNotEquals(404, $httpCode, 'Token endpoint should exist');
    }
}
