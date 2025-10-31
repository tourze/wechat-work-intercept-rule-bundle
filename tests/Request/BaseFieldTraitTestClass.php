<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Request;

use WechatWorkInterceptRuleBundle\Request\BaseFieldTrait;

/**
 * 测试用的具体类，使用BaseFieldTrait trait
 *
 * @internal
 */
class BaseFieldTraitTestClass
{
    use BaseFieldTrait;

    private ?string $agent = null;

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(?string $agent): void
    {
        $this->agent = $agent;
    }
}
