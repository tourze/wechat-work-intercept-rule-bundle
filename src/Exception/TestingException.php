<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Exception;

/**
 * 测试环境专用异常类
 */
class TestingException extends \RuntimeException
{
    public static function notImplementedInStub(string $method = ''): self
    {
        $message = 'Not implemented in testing stub';
        if ('' !== $method) {
            $message .= ': ' . $method;
        }

        return new self($message);
    }
}
