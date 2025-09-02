<?php
namespace WebFiori\Tests\OAuth\Storage;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\Storage\FileTokenStorage;

class FileTokenStorageTest extends TestCase {
    private FileTokenStorage $storage;
    private string $tempDir;

    protected function setUp(): void {
        $this->tempDir = sys_get_temp_dir() . '/oauth_test_' . uniqid();
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

    public function testConstructorCreatesDirectory(): void {
        $this->assertTrue(is_dir($this->tempDir));
    }

    public function testConstructorWithDefaultDirectory(): void {
        $storage = new FileTokenStorage();
        $this->assertInstanceOf(FileTokenStorage::class, $storage);
    }

    public function testStoreToken(): void {
        $tokenData = ['access_token' => 'token123', 'expires_in' => 3600];
        $result = $this->storage->store('test_key', $tokenData);
        
        $this->assertTrue($result);
        $this->assertTrue($this->storage->exists('test_key'));
    }

    public function testRetrieveToken(): void {
        $tokenData = ['access_token' => 'token123', 'expires_in' => 3600];
        $this->storage->store('test_key', $tokenData);
        
        $retrieved = $this->storage->retrieve('test_key');
        $this->assertEquals($tokenData, $retrieved);
    }

    public function testRetrieveNonExistentToken(): void {
        $result = $this->storage->retrieve('non_existent');
        $this->assertNull($result);
    }

    public function testExistsToken(): void {
        $tokenData = ['access_token' => 'token123'];
        $this->storage->store('test_key', $tokenData);
        
        $this->assertTrue($this->storage->exists('test_key'));
        $this->assertFalse($this->storage->exists('non_existent'));
    }

    public function testDeleteToken(): void {
        $tokenData = ['access_token' => 'token123'];
        $this->storage->store('test_key', $tokenData);
        
        $this->assertTrue($this->storage->exists('test_key'));
        
        $result = $this->storage->delete('test_key');
        $this->assertTrue($result);
        $this->assertFalse($this->storage->exists('test_key'));
    }

    public function testDeleteNonExistentToken(): void {
        $result = $this->storage->delete('non_existent');
        $this->assertTrue($result); // Should return true even if file doesn't exist
    }

    public function testFilePathHashing(): void {
        $tokenData = ['access_token' => 'token123'];
        $this->storage->store('test_key', $tokenData);
        
        // Check that file is created with hashed name
        $expectedHash = hash('sha256', 'test_key');
        $expectedFile = $this->tempDir . '/' . $expectedHash . '.json';
        
        $this->assertTrue(file_exists($expectedFile));
    }

    public function testJsonEncoding(): void {
        $tokenData = [
            'access_token' => 'token123',
            'refresh_token' => 'refresh456',
            'expires_in' => 3600,
            'scope' => 'read write'
        ];
        
        $this->storage->store('test_key', $tokenData);
        $retrieved = $this->storage->retrieve('test_key');
        
        $this->assertEquals($tokenData, $retrieved);
    }

    public function testDirectoryPermissions(): void {
        $this->assertTrue(is_readable($this->tempDir));
        $this->assertTrue(is_writable($this->tempDir));
    }
}
