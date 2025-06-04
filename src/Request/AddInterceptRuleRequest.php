<?php

namespace WechatWorkInterceptRuleBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

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
     * @var array 可使用的userid列表。必须为应用可见范围内的成员；最多支持传1000个节点
     */
    private array $applicableUserList = [];

    /**
     * @var array 可使用的部门列表，必须为应用可见范围内的部门；最多支持传1000个节点
     */
    private array $applicableDepartmentList = [];

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/add_intercept_rule';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'rule_name' => $this->getRuleName(),
            'word_list' => $this->getWordList(),
            'semantics_list' => $this->getSemanticsList(),
            'intercept_type' => $this->getInterceptType(),
            'applicable_range' => [],
        ];

        if (empty($this->getApplicableUserList()) && empty($this->getApplicableDepartmentList())) {
            throw new \InvalidArgumentException('userid与department不能同时为不填');
        }
        if (!empty($this->getApplicableUserList())) {
            $json['applicable_range']['user_list'] = $this->getApplicableUserList();
        }
        if (!empty($this->getApplicableDepartmentList())) {
            $json['applicable_range']['department_list'] = $this->getApplicableDepartmentList();
        }

        return [
            'json' => $json,
        ];
    }

    public function getApplicableUserList(): array
    {
        return $this->applicableUserList;
    }

    public function setApplicableUserList(array $applicableUserList): void
    {
        $this->applicableUserList = $applicableUserList;
    }

    public function getApplicableDepartmentList(): array
    {
        return $this->applicableDepartmentList;
    }

    public function setApplicableDepartmentList(array $applicableDepartmentList): void
    {
        $this->applicableDepartmentList = $applicableDepartmentList;
    }
}
