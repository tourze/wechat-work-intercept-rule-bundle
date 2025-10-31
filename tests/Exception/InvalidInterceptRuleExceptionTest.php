<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkInterceptRuleBundle\Exception\InvalidInterceptRuleException;

/**
 * InvalidInterceptRuleException 测试用例
 *
 * 测试自定义异常类的功能
 *
 * @internal
 */
#[CoversClass(InvalidInterceptRuleException::class)]
final class InvalidInterceptRuleExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return InvalidInterceptRuleException::class;
    }

    protected function getParentExceptionClass(): string
    {
        return \InvalidArgumentException::class;
    }
}
