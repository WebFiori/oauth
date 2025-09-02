<?php
namespace WebFiori\Tests\OAuth;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\AuthorizationRequest;
use WebFiori\OAuth\Providers\MicrosoftProvider;

class AuthorizationRequestTest extends TestCase {
    private MicrosoftProvider $provider;
    private AuthorizationRequest $authRequest;

    protected function setUp(): void {
        $this->provider = new MicrosoftProvider('client_id', 'client_secret', 'http://localhost/callback');
        $this->authRequest = new AuthorizationRequest($this->provider);
    }

    public function testConstructor(): void {
        $this->assertInstanceOf(AuthorizationRequest::class, $this->authRequest);
    }

    public function testBuildUrlWithDefaultScopes(): void {
        $url = $this->authRequest->buildUrl();
        
        $this->assertStringContainsString('login.microsoftonline.com', $url);
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('client_id=client_id', $url);
        $this->assertStringContainsString('redirect_uri=http%3A%2F%2Flocalhost%2Fcallback', $url);
        $this->assertStringContainsString('scope=openid+profile+email', $url);
        $this->assertStringContainsString('state=', $url);
    }

    public function testBuildUrlWithCustomScopes(): void {
        $url = $this->authRequest->buildUrl(['read', 'write', 'admin']);
        
        $this->assertStringContainsString('scope=read+write+admin', $url);
    }

    public function testBuildUrlWithEmptyScopes(): void {
        $url = $this->authRequest->buildUrl([]);
        
        // Should use default scopes when empty array provided
        $this->assertStringContainsString('scope=openid+profile+email', $url);
    }

    public function testStateParameterIsRandom(): void {
        $url1 = $this->authRequest->buildUrl();
        $url2 = $this->authRequest->buildUrl();
        
        // Extract state parameters
        preg_match('/state=([^&]+)/', $url1, $matches1);
        preg_match('/state=([^&]+)/', $url2, $matches2);
        
        $this->assertNotEquals($matches1[1], $matches2[1]);
    }

    public function testStateParameterLength(): void {
        $url = $this->authRequest->buildUrl();
        
        preg_match('/state=([^&]+)/', $url, $matches);
        $state = $matches[1];
        
        // State should be 32 characters (16 bytes hex encoded)
        $this->assertEquals(32, strlen($state));
    }

    public function testUrlParametersAreEncoded(): void {
        $url = $this->authRequest->buildUrl();
        
        $this->assertStringContainsString('redirect_uri=http%3A%2F%2Flocalhost%2Fcallback', $url);
        $this->assertStringContainsString('scope=openid+profile+email', $url);
    }
}
