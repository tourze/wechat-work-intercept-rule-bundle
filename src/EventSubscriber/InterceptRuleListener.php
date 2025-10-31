<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Service\WorkService;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Request\AddInterceptRuleRequest;
use WechatWorkInterceptRuleBundle\Request\DeleteInterceptRuleRequest;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleDetailRequest;
use WechatWorkInterceptRuleBundle\Request\UpdateInterceptRuleRequest;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: InterceptRule::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: InterceptRule::class)]
#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: InterceptRule::class)]
#[WithMonologChannel(channel: 'wechat_work_intercept_rule')]
class InterceptRuleListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly WorkService $workService,
    ) {
    }

    /**
     * 保存本地记录前，我们先同步一次到远程
     */
    public function prePersist(InterceptRule $rule): void
    {
        if (true !== $rule->isSync()) {
            return;
        }

        $request = new AddInterceptRuleRequest();
        $agent = $rule->getAgent();
        assert($agent instanceof Agent || null === $agent);
        $request->setAgent($agent);
        $request->setRuleName($rule->getName() ?? '');
        $request->setWordList($rule->getWordList());
        $interceptType = $rule->getInterceptType();
        if (null === $interceptType) {
            return;
        }
        $request->setInterceptType((int) $interceptType->value);
        $request->setSemanticsList($rule->getSemanticsList());

        if ([] !== $rule->getApplicableUserList()) {
            $request->setApplicableUserList($rule->getApplicableUserList());
        }
        if ([] !== $rule->getApplicableDepartmentList()) {
            $request->setApplicableDepartmentList($rule->getApplicableDepartmentList());
        }

        $response = $this->workService->request($request);
        if (is_array($response) && isset($response['rule_id'])) {
            $ruleId = $response['rule_id'];
            if (is_string($ruleId)) {
                $rule->setRuleId($ruleId);
            }
        }
    }

    /**
     * 更新时，同步到远程
     */
    public function preUpdate(InterceptRule $rule): void
    {
        // 不同步了，就从远程删除
        if (true !== $rule->isSync()) {
            $this->postRemove($rule);

            return;
        }

        // 没规则的话，我们创建一次
        if (null === $rule->getRuleId()) {
            $this->prePersist($rule);

            return;
        }

        $request = $this->createUpdateRequest($rule);
        $detail = $this->fetchRuleDetail($rule);
        if (null === $detail) {
            return;
        }

        $this->syncUserListChanges($request, $rule, $detail);
        $this->syncDepartmentListChanges($request, $rule, $detail);

        $this->workService->request($request);
    }

    /**
     * 创建更新请求
     */
    private function createUpdateRequest(InterceptRule $rule): UpdateInterceptRuleRequest
    {
        $request = new UpdateInterceptRuleRequest();
        $agent = $rule->getAgent();
        assert($agent instanceof Agent || null === $agent);
        $request->setAgent($agent);
        $ruleId = $rule->getRuleId();
        if (null === $ruleId) {
            throw new \InvalidArgumentException('Rule ID cannot be null');
        }
        $request->setRuleId($ruleId);
        $request->setRuleName($rule->getName() ?? '');
        $request->setWordList($rule->getWordList());
        $interceptType = $rule->getInterceptType();
        if (null === $interceptType) {
            throw new \InvalidArgumentException('Intercept type cannot be null');
        }
        $request->setInterceptType((int) $interceptType->value);
        $request->setSemanticsList($rule->getSemanticsList());

        return $request;
    }

    /**
     * 获取规则详情
     * @return array<string, mixed>|null
     */
    private function fetchRuleDetail(InterceptRule $rule): ?array
    {
        $detailRequest = new GetInterceptRuleDetailRequest();
        $agent = $rule->getAgent();
        assert($agent instanceof Agent || null === $agent);
        $detailRequest->setAgent($agent);
        $ruleId = $rule->getRuleId();
        if (null === $ruleId) {
            return null;
        }
        $detailRequest->setRuleId($ruleId);
        $response = $this->workService->request($detailRequest);

        if (!is_array($response) || !isset($response['rule'])) {
            return null;
        }

        $detail = $response['rule'];
        if (!is_array($detail)) {
            return null;
        }
        /** @var array<string, mixed> $detail */
        return $detail;
    }

    /**
     * 同步用户列表变更
     * @param array<string, mixed> $detail
     */
    private function syncUserListChanges(UpdateInterceptRuleRequest $request, InterceptRule $rule, array $detail): void
    {
        $applicableUserList = $rule->getApplicableUserList();
        $applicableRange = $detail['applicable_range'] ?? [];
        if (!is_array($applicableRange)) {
            $applicableRange = [];
        }
        $remoteUserList = $applicableRange['user_list'] ?? [];
        if (!is_array($remoteUserList)) {
            $remoteUserList = [];
        }

        $addUserList = array_diff($applicableUserList, $remoteUserList);
        $delUserList = array_diff($remoteUserList, $applicableUserList);

        $addUserListTyped = array_values(array_filter($addUserList, 'is_string'));
        $delUserListTyped = array_values(array_filter($delUserList, 'is_string'));

        $request->setAddApplicableUserList([] === $addUserListTyped ? null : $addUserListTyped);
        $request->setRemoveApplicableUserList([] === $delUserListTyped ? null : $delUserListTyped);
    }

    /**
     * 同步部门列表变更
     * @param array<string, mixed> $detail
     */
    private function syncDepartmentListChanges(UpdateInterceptRuleRequest $request, InterceptRule $rule, array $detail): void
    {
        $applicableDepartmentList = $rule->getApplicableDepartmentList();
        $applicableRange = $detail['applicable_range'] ?? [];
        if (!is_array($applicableRange)) {
            $applicableRange = [];
        }
        $remoteDepartmentList = $applicableRange['department_list'] ?? [];
        if (!is_array($remoteDepartmentList)) {
            $remoteDepartmentList = [];
        }

        $addDepartmentList = array_diff($applicableDepartmentList, $remoteDepartmentList);
        $delDepartmentList = array_diff($remoteDepartmentList, $applicableDepartmentList);

        $addDepartmentListTyped = array_map('intval', array_filter($addDepartmentList, 'is_numeric'));
        $delDepartmentListTyped = array_map('intval', array_filter($delDepartmentList, 'is_numeric'));

        $request->setAddApplicableDepartmentList([] === $addDepartmentListTyped ? null : $addDepartmentListTyped);
        $request->setRemoveApplicableDepartmentList([] === $delDepartmentListTyped ? null : $delDepartmentListTyped);
    }

    /**
     * 删除本地记录后，再删远程的记录
     */
    public function postRemove(InterceptRule $object): void
    {
        if (null === $object->getRuleId()) {
            $this->logger->debug('无远程规则id，跳过', [
                'rule' => $object,
            ]);

            return;
        }

        $request = new DeleteInterceptRuleRequest();
        $request->setRuleId($object->getRuleId());
        $agent = $object->getAgent();
        assert($agent instanceof Agent || null === $agent);
        $request->setAgent($agent);
        try {
            $this->workService->request($request);
        } catch (\Throwable $exception) {
            $this->logger->error('从远程删除敏感词规则ID时发生异常', [
                'exception' => $exception,
            ]);
        }
    }
}
