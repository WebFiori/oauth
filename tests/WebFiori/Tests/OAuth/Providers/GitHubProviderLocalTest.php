<?php
namespace WebFiori\Tests\OAuth\Providers;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\GitHubProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;

class GitHubProviderLocalTest extends TestCase {
    private array $config;
    private string $configPath;
    private string $samplePath;

    protected function setUp(): void {
        $this->configPath = __DIR__ . '/../../../../../config/local.php';
        $this->samplePath = __DIR__ . '/../../../../../config/local-sample.php';
        
        $this->ensureConfigExists();
        $this->config = require $this->configPath;
        
        if ($this->isUsingDefaultValues()) {
            $this->markTestSkipped('Configuration is using default values from local-sample.php. Please update config/local.php with real GitHub app credentials.');
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
        return !isset($this->config['github']) ||
               $this->config['github']['client_id'] === 'your-github-client-id-here' ||
               $this->config['github']['client_secret'] === 'your-github-client-secret-here' ||
               str_contains($this->config['github']['client_id'], 'your-github-client-id') ||
               str_contains($this->config['github']['client_secret'], 'your-github-client-secret');
    }

    public function testProviderWithRealConfig(): void {
        $provider = new GitHubProvider(
            $this->config['github']['client_id'],
            $this->config['github']['client_secret'],
            $this->config['github']['redirect_uri']
        );

        $this->assertEquals($this->config['github']['client_id'], $provider->getClientId());
        $this->assertEquals($this->config['github']['client_secret'], $provider->getClientSecret());
        $this->assertEquals($this->config['github']['redirect_uri'], $provider->getRedirectUri());
        
        $this->assertEquals('https://github.com/login/oauth/authorize', $provider->getAuthorizationUrl());
        $this->assertEquals('https://github.com/login/oauth/access_token', $provider->getTokenUrl());
        $this->assertEquals('https://api.github.com/user', $provider->getUserInfoUrl());
    }

    public function testAuthorizationUrlWithRealConfig(): void {
        $provider = new GitHubProvider(
            $this->config['github']['client_id'],
            $this->config['github']['client_secret'],
            $this->config['github']['redirect_uri']
        );

        $client = new OAuth2Client($provider, new FileTokenStorage());
        $authUrl = $client->getAuthorizationUrl(['user:email', 'read:user']);

        $this->assertStringContainsString('github.com/login/oauth/authorize', $authUrl);
        $this->assertStringContainsString('client_id=' . urlencode($this->config['github']['client_id']), $authUrl);
        $this->assertStringContainsString('redirect_uri=' . urlencode($this->config['github']['redirect_uri']), $authUrl);
        $this->assertStringContainsString('scope=user%3Aemail+read%3Auser', $authUrl);
        $this->assertStringContainsString('state=', $authUrl);
    }

    public function testTokenEndpointAccessibility(): void {
        $provider = new GitHubProvider(
            $this->config['github']['client_id'],
            $this->config['github']['client_secret'],
            $this->config['github']['redirect_uri']
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

    public function testRefreshTokenAndGitHubAPI(): void {
        if (!isset($this->config['github']['refresh_token']) || empty($this->config['github']['refresh_token'])) {
            $this->markTestSkipped('No refresh token configured in config/local.php. Add refresh_token key with valid token.');
        }

        $provider = new GitHubProvider(
            $this->config['github']['client_id'],
            $this->config['github']['client_secret'],
            $this->config['github']['redirect_uri']
        );

        $client = new OAuth2Client($provider);
        
        try {
            $tokens = $client->refreshToken($this->config['github']['refresh_token']);
        } catch (Exception $e) {
            $this->markTestSkipped('Refresh token failed: ' . $e->getMessage() . '. Token may be expired.');
        }
        
        $this->assertArrayHasKey('access_token', $tokens);
        $this->assertNotEmpty($tokens['access_token']);
        
        // Test GitHub API call
        $ch = curl_init('https://api.github.com/user');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $tokens['access_token'],
                'User-Agent: OAuth-Test-App'
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            $this->assertStringStartsWith('gho_', $tokens['access_token'], 'Access token should be a valid GitHub token');
            $this->markTestIncomplete('Token refresh successful but API call failed. This may be due to insufficient scopes.');
            return;
        }
        
        $userData = json_decode($response, true);
        $this->assertIsArray($userData);
        $this->assertArrayHasKey('login', $userData, 'GitHub API should return user login');
    }
}
