<?php

declare(strict_types=1);

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
class GetInterceptRuleListRequest extends ApiRequest
{
    use AgentAware;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/get_intercept_rule_list';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }
}
