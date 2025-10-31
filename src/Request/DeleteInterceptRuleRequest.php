<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 删除敏感词规则
 *
 * @see https://developer.work.weixin.qq.com/document/path/96346
 */
class DeleteInterceptRuleRequest extends ApiRequest
{
    use AgentAware;

    private string $ruleId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/del_intercept_rule';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'rule_id' => $this->getRuleId(),
            ],
        ];
    }

    public function getRuleId(): string
    {
        return $this->ruleId;
    }

    public function setRuleId(string $ruleId): void
    {
        $this->ruleId = $ruleId;
    }
}
