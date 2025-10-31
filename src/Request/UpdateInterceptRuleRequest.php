<?php

declare(strict_types=1);

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
     * @var array<string>|null 需要新增的使用范围:可使用的userid列表。必须为应用可见范围内的成员；最多支持传1000个节点
     */
    private ?array $addApplicableUserList = null;

    /**
     * @var array<int>|null 需要新增的使用范围:可使用的部门列表，必须为应用可见范围内的部门；最多支持传1000个节点
     */
    private ?array $addApplicableDepartmentList = null;

    /**
     * @var array<string>|null 需要删除的使用范围:可使用的userid列表。必须为应用可见范围内的成员；最多支持传1000个节点
     */
    private ?array $removeApplicableUserList = null;

    /**
     * @var array<int>|null 需要删除的使用范围:可使用的部门列表，必须为应用可见范围内的部门；最多支持传1000个节点
     */
    private ?array $removeApplicableDepartmentList = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/update_intercept_rule';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $json = $this->buildBaseJson();

        $json = $this->addApplicableRange($json);
        $json = $this->addRemoveApplicableRange($json);

        return [
            'json' => $json,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildBaseJson(): array
    {
        /** @var array<string, mixed> $json */
        $json = [
            'rule_id' => $this->getRuleId(),
        ];

        $json = $this->addBasicFields($json);
        $json = $this->addExtraRule($json);

        return $json;
    }

    /**
     * @param array<string, mixed> $json
     * @return array<string, mixed>
     */
    private function addBasicFields(array $json): array
    {
        if (isset($this->ruleName)) {
            $json['rule_name'] = $this->getRuleName();
        }
        if (isset($this->wordList)) {
            $json['word_list'] = $this->getWordList();
        }
        if (isset($this->interceptType)) {
            $json['intercept_type'] = $this->getInterceptType();
        }

        return $json;
    }

    /**
     * @param array<string, mixed> $json
     * @return array<string, mixed>
     */
    private function addExtraRule(array $json): array
    {
        if (isset($this->semanticsList)) {
            $json['extra_rule'] = [
                'semantics_list' => $this->getSemanticsList(),
            ];
        }

        return $json;
    }

    /**
     * @param array<string, mixed> $json
     * @return array<string, mixed>
     */
    private function addApplicableRange(array $json): array
    {
        $addRange = [];

        $addUserList = $this->getAddApplicableUserList();
        if ($addUserList !== [] && $addUserList !== null) {
            $addRange['user_list'] = $addUserList;
        }

        $addDepartmentList = $this->getAddApplicableDepartmentList();
        if ($addDepartmentList !== [] && $addDepartmentList !== null) {
            $addRange['department_list'] = $addDepartmentList;
        }

        if ($addRange !== []) {
            $json['add_applicable_range'] = $addRange;
        }

        return $json;
    }

    /**
     * @param array<string, mixed> $json
     * @return array<string, mixed>
     */
    private function addRemoveApplicableRange(array $json): array
    {
        $removeRange = [];

        $removeUserList = $this->getRemoveApplicableUserList();
        if ($removeUserList !== [] && $removeUserList !== null) {
            $removeRange['user_list'] = $removeUserList;
        }

        $removeDepartmentList = $this->getRemoveApplicableDepartmentList();
        if ($removeDepartmentList !== [] && $removeDepartmentList !== null) {
            $removeRange['department_list'] = $removeDepartmentList;
        }

        if ($removeRange !== []) {
            $json['remove_applicable_range'] = $removeRange;
        }

        return $json;
    }

    public function getRuleId(): string
    {
        return $this->ruleId;
    }

    public function setRuleId(string $ruleId): void
    {
        $this->ruleId = $ruleId;
    }

    /**
     * @return array<string>|null
     */
    public function getAddApplicableUserList(): ?array
    {
        return $this->addApplicableUserList;
    }

    /**
     * @param array<string>|null $addApplicableUserList
     */
    public function setAddApplicableUserList(?array $addApplicableUserList): void
    {
        $this->addApplicableUserList = $addApplicableUserList;
    }

    /**
     * @return array<int>|null
     */
    public function getAddApplicableDepartmentList(): ?array
    {
        return $this->addApplicableDepartmentList;
    }

    /**
     * @param array<int>|null $addApplicableDepartmentList
     */
    public function setAddApplicableDepartmentList(?array $addApplicableDepartmentList): void
    {
        $this->addApplicableDepartmentList = $addApplicableDepartmentList;
    }

    /**
     * @return array<string>|null
     */
    public function getRemoveApplicableUserList(): ?array
    {
        return $this->removeApplicableUserList;
    }

    /**
     * @param array<string>|null $removeApplicableUserList
     */
    public function setRemoveApplicableUserList(?array $removeApplicableUserList): void
    {
        $this->removeApplicableUserList = $removeApplicableUserList;
    }

    /**
     * @return array<int>|null
     */
    public function getRemoveApplicableDepartmentList(): ?array
    {
        return $this->removeApplicableDepartmentList;
    }

    /**
     * @param array<int>|null $removeApplicableDepartmentList
     */
    public function setRemoveApplicableDepartmentList(?array $removeApplicableDepartmentList): void
    {
        $this->removeApplicableDepartmentList = $removeApplicableDepartmentList;
    }
}
