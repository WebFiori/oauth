<?php
namespace WebFiori\Tests\OAuth;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\OAuthManager;
use WebFiori\OAuth\OAuth2Client;
use WebFiori\OAuth\Providers\MicrosoftProvider;
use WebFiori\OAuth\Storage\FileTokenStorage;
use WebFiori\OAuth\Exceptions\OAuth2Exception;

class OAuthManagerTest extends TestCase {
    private OAuthManager $manager;
    private MicrosoftProvider $provider;
    private FileTokenStorage $storage;

    protected function setUp(): void {
        $this->storage = new FileTokenStorage();
        $this->manager = new OAuthManager($this->storage);
        $this->provider = new MicrosoftProvider('client-id', 'client-secret', 'http://localhost/callback');
    }

    public function testAddProvider(): void {
        $this->manager->addProvider('microsoft', $this->provider);
        
        $this->assertTrue($this->manager->hasProvider('microsoft'));
        $this->assertContains('microsoft', $this->manager->getProviderNames());
    }

    public function testGetClient(): void {
        $this->manager->addProvider('microsoft', $this->provider);
        
        $client = $this->manager->getClient('microsoft');
        
        $this->assertInstanceOf(OAuth2Client::class, $client);
    }

    public function testGetClientThrowsExceptionForUnknownProvider(): void {
        $this->expectException(OAuth2Exception::class);
        $this->expectExceptionMessage("Provider 'unknown' not found");
        
        $this->manager->getClient('unknown');
    }

    public function testRemoveProvider(): void {
        $this->manager->addProvider('microsoft', $this->provider);
        $this->assertTrue($this->manager->hasProvider('microsoft'));
        
        $this->manager->removeProvider('microsoft');
        $this->assertFalse($this->manager->hasProvider('microsoft'));
    }

    public function testGetProviderNames(): void {
        $this->manager->addProvider('microsoft', $this->provider);
        $this->manager->addProvider('google', $this->provider);
        
        $names = $this->manager->getProviderNames();
        
        $this->assertCount(2, $names);
        $this->assertContains('microsoft', $names);
        $this->assertContains('google', $names);
    }

    public function testSetStorage(): void {
        $newStorage = new FileTokenStorage('/tmp');
        
        $result = $this->manager->setStorage($newStorage);
        
        $this->assertSame($this->manager, $result);
    }

    public function testChainedOperations(): void {
        $result = $this->manager
            ->addProvider('microsoft', $this->provider)
            ->addProvider('google', $this->provider);
        
        $this->assertSame($this->manager, $result);
        $this->assertCount(2, $this->manager->getProviderNames());
    }
}
