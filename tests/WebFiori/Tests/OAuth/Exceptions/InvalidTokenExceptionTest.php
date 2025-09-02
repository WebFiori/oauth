<?php
namespace WebFiori\Tests\OAuth\Exceptions;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\Exceptions\InvalidTokenException;
use WebFiori\OAuth\Exceptions\OAuth2Exception;

class InvalidTokenExceptionTest extends TestCase {
    public function testExceptionInheritance(): void {
        $exception = new InvalidTokenException('Invalid token');
        $this->assertInstanceOf(OAuth2Exception::class, $exception);
    }

    public function testExceptionMessage(): void {
        $message = 'Token is invalid or expired';
        $exception = new InvalidTokenException($message);
        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionCode(): void {
        $code = 401;
        $exception = new InvalidTokenException('Invalid token', $code);
        $this->assertEquals($code, $exception->getCode());
    }
}
