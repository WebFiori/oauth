<?php
namespace WebFiori\Tests\OAuth\Exceptions;

use PHPUnit\Framework\TestCase;
use WebFiori\OAuth\Exceptions\OAuth2Exception;
use Exception;

class OAuth2ExceptionTest extends TestCase {
    public function testExceptionInheritance(): void {
        $exception = new OAuth2Exception('Test message');
        $this->assertInstanceOf(Exception::class, $exception);
    }

    public function testExceptionMessage(): void {
        $message = 'OAuth2 error occurred';
        $exception = new OAuth2Exception($message);
        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionCode(): void {
        $code = 400;
        $exception = new OAuth2Exception('Test message', $code);
        $this->assertEquals($code, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void {
        $previous = new Exception('Previous exception');
        $exception = new OAuth2Exception('Test message', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }
}
