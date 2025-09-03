<?php
namespace WebFiori\Tests\OAuth\Exceptions;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\Exceptions\InvalidTokenException;

class InvalidTokenExceptionTest extends TestCase {
    public function testExceptionCreation(): void {
        $exception = new InvalidTokenException('Invalid token');
        
        $this->assertInstanceOf(InvalidTokenException::class, $exception);
        $this->assertEquals('Invalid token', $exception->getMessage());
    }

    public function testExceptionWithCode(): void {
        $exception = new InvalidTokenException('Token expired', 401);
        
        $this->assertEquals('Token expired', $exception->getMessage());
        $this->assertEquals(401, $exception->getCode());
    }
}
