<?php

namespace WechatWorkInterceptRuleBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
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
        if (!$rule->isSync()) {
            return;
        }

        $request = new AddInterceptRuleRequest();
        /** @var Agent|null $agent */
        $agent = $rule->getAgent();
        $request->setAgent($agent);
        $request->setRuleName($rule->getName());
        $request->setWordList($rule->getWordList());
        $request->setInterceptType((int)$rule->getInterceptType()->value);
        $request->setSemanticsList($rule->getSemanticsList() ?? []);

        if (!empty($rule->getApplicableUserList())) {
            $request->setApplicableUserList($rule->getApplicableUserList());
        }
        if (!empty($rule->getApplicableDepartmentList())) {
            $request->setApplicableDepartmentList($rule->getApplicableDepartmentList());
        }

        $response = $this->workService->request($request);
        if (isset($response['rule_id'])) {
            $rule->setRuleId($response['rule_id']);
        }
    }

    /**
     * 更新时，同步到远程
     */
    public function preUpdate(InterceptRule $rule): void
    {
        // 不同步了，就从远程删除
        if (!$rule->isSync()) {
            $this->postRemove($rule);

            return;
        }

        // 没规则的话，我们创建一次
        if ($rule->getRuleId() === null) {
            $this->prePersist($rule);

            return;
        }

        $request = new UpdateInterceptRuleRequest();
        /** @var Agent|null $agent */
        $agent = $rule->getAgent();
        $request->setAgent($agent);
        $request->setRuleId($rule->getRuleId());
        $request->setRuleName($rule->getName());
        $request->setWordList($rule->getWordList());
        $request->setInterceptType((int)$rule->getInterceptType()->value);
        $request->setSemanticsList($rule->getSemanticsList() ?? []);

        // 编辑的话，需要进行一次对比
        $detailRequest = new GetInterceptRuleDetailRequest();
        /** @var Agent|null $agent */
        $agent = $rule->getAgent();
        $detailRequest->setAgent($agent);
        $detailRequest->setRuleId($rule->getRuleId());
        $detail = $this->workService->request($detailRequest)['rule'];

        $addUserList = array_diff($rule->getApplicableUserList(), $detail['applicable_range']['user_list']);
        $delUserList = array_diff($detail['applicable_range']['user_list'], $rule->getApplicableUserList());
        $request->setAddApplicableUserList($addUserList);
        $request->setRemoveApplicableUserList($delUserList);

        $addDepartmentList = array_diff($rule->getApplicableDepartmentList(), $detail['applicable_range']['department_list']);
        $delDepartmentList = array_diff($detail['applicable_range']['department_list'], $rule->getApplicableDepartmentList());

        $request->setAddApplicableDepartmentList($addDepartmentList);
        $request->setRemoveApplicableDepartmentList($delDepartmentList);

        $this->workService->request($request);
    }

    /**
     * 删除本地记录后，再删远程的记录
     */
    public function postRemove(InterceptRule $object): void
    {
        if ($object->getRuleId() === null) {
            $this->logger->debug('无远程规则id，跳过', [
                'rule' => $this,
            ]);

            return;
        }

        $request = new DeleteInterceptRuleRequest();
        $request->setRuleId($object->getRuleId());
        /** @var Agent|null $agent */
        $agent = $object->getAgent();
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
