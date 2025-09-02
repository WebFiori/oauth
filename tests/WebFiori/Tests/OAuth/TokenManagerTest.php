<?php
namespace WebFiori\Tests\OAuth;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\TokenManager;
use WebFiori\OAuth\Storage\TokenStorage;

class TokenManagerTest extends TestCase {
    private TokenStorage $storage;
    private TokenManager $tokenManager;

    protected function setUp(): void {
        $this->storage = $this->createMock(TokenStorage::class);
        $this->tokenManager = new TokenManager($this->storage);
    }

    public function testConstructor(): void {
        $this->assertInstanceOf(TokenManager::class, $this->tokenManager);
    }

    public function testStore(): void {
        $tokenData = ['access_token' => 'token123', 'expires_in' => 3600];
        
        $this->storage->expects($this->once())
            ->method('store')
            ->with('test_key', $tokenData)
            ->willReturn(true);

        $this->tokenManager->store('test_key', $tokenData);
    }

    public function testRetrieve(): void {
        $tokenData = ['access_token' => 'token123', 'expires_in' => 3600];
        
        $this->storage->expects($this->once())
            ->method('retrieve')
            ->with('test_key')
            ->willReturn($tokenData);

        $result = $this->tokenManager->retrieve('test_key');
        $this->assertEquals($tokenData, $result);
    }

    public function testRetrieveNonExistent(): void {
        $this->storage->expects($this->once())
            ->method('retrieve')
            ->with('non_existent_key')
            ->willReturn(null);

        $result = $this->tokenManager->retrieve('non_existent_key');
        $this->assertNull($result);
    }

    public function testDelete(): void {
        $this->storage->expects($this->once())
            ->method('delete')
            ->with('test_key')
            ->willReturn(true);

        $this->tokenManager->delete('test_key');
    }
}
