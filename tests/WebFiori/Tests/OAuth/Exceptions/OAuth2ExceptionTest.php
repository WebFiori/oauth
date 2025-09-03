<?php
namespace WebFiori\Tests\OAuth\Exceptions;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\Exceptions\OAuth2Exception;

class OAuth2ExceptionTest extends TestCase {
    public function testExceptionCreation(): void {
        $exception = new OAuth2Exception('Test message');
        
        $this->assertInstanceOf(OAuth2Exception::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testExceptionWithCode(): void {
        $exception = new OAuth2Exception('Test message', 400);
        
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
    }
}
