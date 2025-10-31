<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkInterceptRuleBundle\Exception\InvalidInterceptRuleException;

/**
 * 新建敏感词规则
 *
 * @see https://developer.work.weixin.qq.com/document/path/96346
 */
class AddInterceptRuleRequest extends ApiRequest
{
    use AgentAware;
    use BaseFieldTrait;

    /**
     * @var array<string> 可使用的userid列表。必须为应用可见范围内的成员；最多支持传1000个节点
     */
    private array $applicableUserList = [];

    /**
     * @var array<int> 可使用的部门列表，必须为应用可见范围内的部门；最多支持传1000个节点
     */
    private array $applicableDepartmentList = [];

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/add_intercept_rule';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $json = [
            'rule_name' => $this->getRuleName(),
            'word_list' => $this->getWordList(),
            'semantics_list' => $this->getSemanticsList(),
            'intercept_type' => $this->getInterceptType(),
            'applicable_range' => [],
        ];

        if ([] === $this->getApplicableUserList() && [] === $this->getApplicableDepartmentList()) {
            throw new InvalidInterceptRuleException('userid与department不能同时为不填');
        }
        if ([] !== $this->getApplicableUserList()) {
            $json['applicable_range']['user_list'] = $this->getApplicableUserList();
        }
        if ([] !== $this->getApplicableDepartmentList()) {
            $json['applicable_range']['department_list'] = $this->getApplicableDepartmentList();
        }

        return [
            'json' => $json,
        ];
    }

    /**
     * @return array<string>
     */
    public function getApplicableUserList(): array
    {
        return $this->applicableUserList;
    }

    /**
     * @param array<string> $applicableUserList
     */
    public function setApplicableUserList(array $applicableUserList): void
    {
        $this->applicableUserList = $applicableUserList;
    }

    /**
     * @return array<int>
     */
    public function getApplicableDepartmentList(): array
    {
        return $this->applicableDepartmentList;
    }

    /**
     * @param array<int> $applicableDepartmentList
     */
    public function setApplicableDepartmentList(array $applicableDepartmentList): void
    {
        $this->applicableDepartmentList = $applicableDepartmentList;
    }
}
