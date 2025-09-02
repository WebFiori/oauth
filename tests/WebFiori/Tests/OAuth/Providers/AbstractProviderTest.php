<?php
namespace WebFiori\Tests\OAuth\Providers;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\Providers\AbstractProvider;

class AbstractProviderTest extends TestCase {
    private AbstractProvider $provider;

    protected function setUp(): void {
        $this->provider = new class('client_id', 'client_secret', 'http://localhost/callback') extends AbstractProvider {
            public function getAuthorizationUrl(): string {
                return 'https://example.com/authorize';
            }

            public function getTokenUrl(): string {
                return 'https://example.com/token';
            }

            public function getUserInfoUrl(): string {
                return 'https://example.com/user';
            }

            public function getDefaultScopes(): array {
                return ['read', 'write'];
            }
        };
    }

    public function testConstructor(): void {
        $this->assertInstanceOf(AbstractProvider::class, $this->provider);
    }

    public function testGetClientId(): void {
        $this->assertEquals('client_id', $this->provider->getClientId());
    }

    public function testGetClientSecret(): void {
        $this->assertEquals('client_secret', $this->provider->getClientSecret());
    }

    public function testGetRedirectUri(): void {
        $this->assertEquals('http://localhost/callback', $this->provider->getRedirectUri());
    }

    public function testAbstractMethodsAreImplemented(): void {
        $this->assertEquals('https://example.com/authorize', $this->provider->getAuthorizationUrl());
        $this->assertEquals('https://example.com/token', $this->provider->getTokenUrl());
        $this->assertEquals('https://example.com/user', $this->provider->getUserInfoUrl());
        $this->assertEquals(['read', 'write'], $this->provider->getDefaultScopes());
    }
}
