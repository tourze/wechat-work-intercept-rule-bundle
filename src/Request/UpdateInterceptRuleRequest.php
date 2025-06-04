<?php

namespace WechatWorkInterceptRuleBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 修改敏感词规则
 *
 * @see https://developer.work.weixin.qq.com/document/path/96346
 */
class UpdateInterceptRuleRequest extends ApiRequest
{
    use AgentAware;
    use BaseFieldTrait;

    private string $ruleId;

    /**
     * @var array|null 需要新增的使用范围:可使用的userid列表。必须为应用可见范围内的成员；最多支持传1000个节点
     */
    private ?array $addApplicableUserList = null;

    /**
     * @var array|null 需要新增的使用范围:可使用的部门列表，必须为应用可见范围内的部门；最多支持传1000个节点
     */
    private ?array $addApplicableDepartmentList = null;

    /**
     * @var array|null 需要删除的使用范围:可使用的userid列表。必须为应用可见范围内的成员；最多支持传1000个节点
     */
    private ?array $removeApplicableUserList = null;

    /**
     * @var array|null 需要删除的使用范围:可使用的部门列表，必须为应用可见范围内的部门；最多支持传1000个节点
     */
    private ?array $removeApplicableDepartmentList = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/update_intercept_rule';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'rule_id' => $this->getRuleId(),
        ];

        if (isset($this->ruleName)) {
            $json['rule_name'] = $this->getRuleName();
        }
        if (isset($this->wordList)) {
            $json['word_list'] = $this->getWordList();
        }
        if (isset($this->interceptType)) {
            $json['intercept_type'] = $this->getInterceptType();
        }

        if (isset($this->semanticsList)) {
            $json['extra_rule'] = [
                'semantics_list' => $this->getSemanticsList(),
            ];
        }

        if (!empty($this->getAddApplicableUserList())) {
            $json['add_applicable_range']['user_list'] = $this->getAddApplicableUserList();
        }
        if (!empty($this->getAddApplicableDepartmentList())) {
            $json['add_applicable_range']['department_list'] = $this->getAddApplicableDepartmentList();
        }
        if (!empty($this->getRemoveApplicableUserList())) {
            $json['remove_applicable_range']['user_list'] = $this->getRemoveApplicableUserList();
        }
        if (!empty($this->getRemoveApplicableDepartmentList())) {
            $json['remove_applicable_range']['department_list'] = $this->getRemoveApplicableDepartmentList();
        }

        return [
            'json' => $json,
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

    public function getAddApplicableUserList(): ?array
    {
        return $this->addApplicableUserList;
    }

    public function setAddApplicableUserList(?array $addApplicableUserList): void
    {
        $this->addApplicableUserList = $addApplicableUserList;
    }

    public function getAddApplicableDepartmentList(): ?array
    {
        return $this->addApplicableDepartmentList;
    }

    public function setAddApplicableDepartmentList(?array $addApplicableDepartmentList): void
    {
        $this->addApplicableDepartmentList = $addApplicableDepartmentList;
    }

    public function getRemoveApplicableUserList(): ?array
    {
        return $this->removeApplicableUserList;
    }

    public function setRemoveApplicableUserList(?array $removeApplicableUserList): void
    {
        $this->removeApplicableUserList = $removeApplicableUserList;
    }

    public function getRemoveApplicableDepartmentList(): ?array
    {
        return $this->removeApplicableDepartmentList;
    }

    public function setRemoveApplicableDepartmentList(?array $removeApplicableDepartmentList): void
    {
        $this->removeApplicableDepartmentList = $removeApplicableDepartmentList;
    }
}
