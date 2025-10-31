<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkInterceptRuleBundle\Exception\TestingException;

/**
 * @internal
 */
#[CoversClass(TestingException::class)]
final class TestingExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return TestingException::class;
    }

    protected function getParentExceptionClass(): string
    {
        return \RuntimeException::class;
    }

    public function testNotImplementedInStubWithoutMethod(): void
    {
        $exception = TestingException::notImplementedInStub();

        $this->assertInstanceOf(TestingException::class, $exception);
        $this->assertSame('Not implemented in testing stub', $exception->getMessage());
    }

    public function testNotImplementedInStubWithMethod(): void
    {
        $method = 'getManager';
        $exception = TestingException::notImplementedInStub($method);

        $this->assertInstanceOf(TestingException::class, $exception);
        $this->assertSame('Not implemented in testing stub: getManager', $exception->getMessage());
    }

    public function testNotImplementedInStubWithEmptyMethod(): void
    {
        $exception = TestingException::notImplementedInStub('');

        $this->assertInstanceOf(TestingException::class, $exception);
        $this->assertSame('Not implemented in testing stub', $exception->getMessage());
    }

    public function testExceptionCanBeCaught(): void
    {
        $this->expectException(TestingException::class);
        $this->expectExceptionMessage('Not implemented in testing stub: testMethod');

        throw TestingException::notImplementedInStub('testMethod');
    }

    public function testMultipleExceptionsHaveDifferentMessages(): void
    {
        $exception1 = TestingException::notImplementedInStub('method1');
        $exception2 = TestingException::notImplementedInStub('method2');

        $this->assertNotSame($exception1->getMessage(), $exception2->getMessage());
        $this->assertStringContainsString('method1', $exception1->getMessage());
        $this->assertStringContainsString('method2', $exception2->getMessage());
    }

    public function testExceptionWithComplexMethodName(): void
    {
        $method = 'getSomeComplexMethodName';
        $exception = TestingException::notImplementedInStub($method);

        $this->assertStringContainsString($method, $exception->getMessage());
        $this->assertSame(
            'Not implemented in testing stub: getSomeComplexMethodName',
            $exception->getMessage()
        );
    }

    public function testExceptionCreationIsImmutable(): void
    {
        $method = 'testMethod';
        $exception1 = TestingException::notImplementedInStub($method);
        $exception2 = TestingException::notImplementedInStub($method);

        $this->assertNotSame($exception1, $exception2);
        $this->assertSame($exception1->getMessage(), $exception2->getMessage());
    }
}
