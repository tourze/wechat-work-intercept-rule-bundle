<?php

namespace WechatWorkInterceptRuleBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use WechatWorkInterceptRuleBundle\Exception\InvalidInterceptRuleException;

/**
 * InvalidInterceptRuleException 测试用例
 *
 * 测试自定义异常类的功能
 */
class InvalidInterceptRuleExceptionTest extends TestCase
{
    public function test_exceptionIsInstanceOfInvalidArgumentException(): void
    {
        $exception = new InvalidInterceptRuleException();
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }

    public function test_exceptionCanBeCreatedWithMessage(): void
    {
        $message = 'Invalid intercept rule configuration';
        $exception = new InvalidInterceptRuleException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }

    public function test_exceptionCanBeCreatedWithMessageAndCode(): void
    {
        $message = 'Invalid intercept rule configuration';
        $code = 400;
        $exception = new InvalidInterceptRuleException($message, $code);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function test_exceptionCanBeCreatedWithMessageCodeAndPrevious(): void
    {
        $message = 'Invalid intercept rule configuration';
        $code = 400;
        $previous = new \RuntimeException('Previous exception');
        $exception = new InvalidInterceptRuleException($message, $code, $previous);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function test_exceptionCanBeThrown(): void
    {
        $this->expectException(InvalidInterceptRuleException::class);
        $this->expectExceptionMessage('Test exception');
        
        throw new InvalidInterceptRuleException('Test exception');
    }

    public function test_exceptionCanBeCaught(): void
    {
        try {
            throw new InvalidInterceptRuleException('Test exception');
        } catch (InvalidInterceptRuleException $e) {
            $this->assertEquals('Test exception', $e->getMessage());
        }
    }

    public function test_exceptionCanBeCaughtAsInvalidArgumentException(): void
    {
        try {
            throw new InvalidInterceptRuleException('Test exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('Test exception', $e->getMessage());
            $this->assertInstanceOf(InvalidInterceptRuleException::class, $e);
        }
    }

    public function test_exceptionWithoutArguments(): void
    {
        $exception = new InvalidInterceptRuleException();
        
        $this->assertEquals('', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function test_exceptionStackTrace(): void
    {
        $exception = new InvalidInterceptRuleException('Test exception');
        $trace = $exception->getTrace();
        
        // Trace is always an array, no need to assert
        $this->assertNotEmpty($trace);
        $this->assertArrayHasKey('file', $trace[0]);
        $this->assertArrayHasKey('line', $trace[0]);
        $this->assertArrayHasKey('function', $trace[0]);
    }

    public function test_exceptionToString(): void
    {
        $exception = new InvalidInterceptRuleException('Test exception');
        $string = (string) $exception;
        
        $this->assertStringContainsString('InvalidInterceptRuleException', $string);
        $this->assertStringContainsString('Test exception', $string);
    }

    public function test_exceptionFile(): void
    {
        $exception = new InvalidInterceptRuleException('Test exception');
        
        $this->assertStringEndsWith('InvalidInterceptRuleExceptionTest.php', $exception->getFile());
        // Line is always an int, just check it's greater than 0
        $this->assertGreaterThan(0, $exception->getLine());
    }


    public function test_exceptionSerialization(): void
    {
        $exception = new InvalidInterceptRuleException('Serializable exception', 200);
        $serialized = serialize($exception);
        /** @var InvalidInterceptRuleException $unserialized */
        $unserialized = unserialize($serialized);
        
        $this->assertInstanceOf(InvalidInterceptRuleException::class, $unserialized);
        $this->assertEquals($exception->getMessage(), $unserialized->getMessage());
        $this->assertEquals($exception->getCode(), $unserialized->getCode());
    }

    public function test_exceptionInheritance(): void
    {
        $exception = new InvalidInterceptRuleException();
        
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertInstanceOf(InvalidInterceptRuleException::class, $exception);
    }
}