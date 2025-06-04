<?php

namespace WechatWorkInterceptRuleBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取敏感词规则列表
 *
 * 企业和第三方应用可以通过此接口获取敏感词规则列表
 *
 * @see https://developer.work.weixin.qq.com/document/path/96346
 */
class GetInterceptRuleDetailRequest extends ApiRequest
{
    use AgentAware;

    private string $ruleId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/get_intercept_rule';
    }

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
