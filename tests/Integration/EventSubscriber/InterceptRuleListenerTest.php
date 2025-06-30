<?php

namespace WechatWorkInterceptRuleBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Service\WorkService;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;
use WechatWorkInterceptRuleBundle\EventSubscriber\InterceptRuleListener;
use WechatWorkInterceptRuleBundle\Request\AddInterceptRuleRequest;
use WechatWorkInterceptRuleBundle\Request\DeleteInterceptRuleRequest;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleDetailRequest;
use WechatWorkInterceptRuleBundle\Request\UpdateInterceptRuleRequest;

/**
 * InterceptRuleListener 测试用例
 *
 * 测试事件监听器的所有方法
 */
class InterceptRuleListenerTest extends TestCase
{
    private LoggerInterface&MockObject $logger;
    private WorkService&MockObject $workService;
    private InterceptRuleListener $listener;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->listener = new InterceptRuleListener($this->logger, $this->workService);
    }

    public function test_prePersist_withSyncEnabled_createsRemoteRule(): void
    {
        $agent = $this->createMock(Agent::class);
        $rule = $this->createMock(InterceptRule::class);
        
        $rule->expects($this->once())->method('isSync')->willReturn(true);
        $rule->expects($this->once())->method('getAgent')->willReturn($agent);
        $rule->expects($this->once())->method('getName')->willReturn('Test Rule');
        $rule->expects($this->once())->method('getWordList')->willReturn(['word1', 'word2']);
        $rule->expects($this->once())->method('getInterceptType')->willReturn(InterceptType::WARN);
        $rule->expects($this->once())->method('getSemanticsList')->willReturn([]);
        $rule->expects($this->exactly(2))->method('getApplicableUserList')->willReturn(['user1']);
        $rule->expects($this->exactly(2))->method('getApplicableDepartmentList')->willReturn(['dept1']);
        $rule->expects($this->once())->method('setRuleId')->with('rule_123');

        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->isInstanceOf(AddInterceptRuleRequest::class))
            ->willReturn(['rule_id' => 'rule_123']);

        $this->listener->prePersist($rule);
    }

    public function test_prePersist_withSyncDisabled_skipsRemoteCreation(): void
    {
        $rule = $this->createMock(InterceptRule::class);
        $rule->expects($this->once())->method('isSync')->willReturn(false);
        $rule->expects($this->never())->method('getAgent');

        $this->workService->expects($this->never())->method('request');

        $this->listener->prePersist($rule);
    }

    public function test_prePersist_withEmptyApplicableLists_createsWithoutLists(): void
    {
        $agent = $this->createMock(Agent::class);
        $rule = $this->createMock(InterceptRule::class);
        
        $rule->expects($this->once())->method('isSync')->willReturn(true);
        $rule->expects($this->once())->method('getAgent')->willReturn($agent);
        $rule->expects($this->once())->method('getName')->willReturn('Test Rule');
        $rule->expects($this->once())->method('getWordList')->willReturn(['word1']);
        $rule->expects($this->once())->method('getInterceptType')->willReturn(InterceptType::NOTICE);
        $rule->expects($this->once())->method('getSemanticsList')->willReturn(null);
        $rule->expects($this->once())->method('getApplicableUserList')->willReturn([]);
        $rule->expects($this->once())->method('getApplicableDepartmentList')->willReturn([]);

        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) {
                return $request instanceof AddInterceptRuleRequest
                    && $request->getRuleName() === 'Test Rule';
            }))
            ->willReturn([]);

        $this->listener->prePersist($rule);
    }

    public function test_preUpdate_withSyncDisabled_deletesRemoteRule(): void
    {
        $agent = $this->createMock(Agent::class);
        $rule = $this->createMock(InterceptRule::class);
        
        $rule->expects($this->once())->method('isSync')->willReturn(false);
        $rule->expects($this->exactly(2))->method('getRuleId')->willReturn('rule_123');
        $rule->expects($this->once())->method('getAgent')->willReturn($agent);

        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->isInstanceOf(DeleteInterceptRuleRequest::class));

        $this->listener->preUpdate($rule);
    }

    public function test_preUpdate_withoutRuleId_createsNewRule(): void
    {
        $agent = $this->createMock(Agent::class);
        $rule = $this->createMock(InterceptRule::class);
        
        $rule->expects($this->exactly(2))->method('isSync')->willReturn(true);
        $rule->expects($this->once())->method('getRuleId')->willReturn(null);
        $rule->expects($this->once())->method('getAgent')->willReturn($agent);
        $rule->expects($this->once())->method('getName')->willReturn('Test Rule');
        $rule->expects($this->once())->method('getWordList')->willReturn(['word1']);
        $rule->expects($this->once())->method('getInterceptType')->willReturn(InterceptType::NOTICE);
        $rule->expects($this->once())->method('getSemanticsList')->willReturn([]);
        $rule->expects($this->once())->method('getApplicableUserList')->willReturn([]);
        $rule->expects($this->once())->method('getApplicableDepartmentList')->willReturn([]);

        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->isInstanceOf(AddInterceptRuleRequest::class));

        $this->listener->preUpdate($rule);
    }

    public function test_preUpdate_withExistingRule_updatesRemoteRule(): void
    {
        $agent = $this->createMock(Agent::class);
        $rule = $this->createMock(InterceptRule::class);
        
        $rule->expects($this->once())->method('isSync')->willReturn(true);
        $rule->expects($this->exactly(3))->method('getRuleId')->willReturn('rule_123');
        $rule->expects($this->exactly(2))->method('getAgent')->willReturn($agent);
        $rule->expects($this->once())->method('getName')->willReturn('Updated Rule');
        $rule->expects($this->once())->method('getWordList')->willReturn(['word1', 'word2']);
        $rule->expects($this->once())->method('getInterceptType')->willReturn(InterceptType::WARN);
        $rule->expects($this->once())->method('getSemanticsList')->willReturn([]);
        $rule->expects($this->exactly(2))->method('getApplicableUserList')->willReturn(['user1', 'user2']);
        $rule->expects($this->exactly(2))->method('getApplicableDepartmentList')->willReturn(['dept1']);

        $detailResponse = [
            'rule' => [
                'applicable_range' => [
                    'user_list' => ['user1', 'user3'],
                    'department_list' => ['dept1', 'dept2']
                ]
            ]
        ];

        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls($detailResponse, []);

        $this->listener->preUpdate($rule);
    }

    public function test_postRemove_withRuleId_deletesRemoteRule(): void
    {
        $agent = $this->createMock(Agent::class);
        $rule = $this->createMock(InterceptRule::class);
        
        $rule->expects($this->exactly(2))->method('getRuleId')->willReturn('rule_123');
        $rule->expects($this->once())->method('getAgent')->willReturn($agent);

        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->isInstanceOf(DeleteInterceptRuleRequest::class));

        $this->listener->postRemove($rule);
    }

    public function test_postRemove_withoutRuleId_skipsRemoteDeletion(): void
    {
        $rule = $this->createMock(InterceptRule::class);
        
        $rule->expects($this->once())->method('getRuleId')->willReturn(null);
        $rule->expects($this->never())->method('getAgent');

        $this->logger->expects($this->once())
            ->method('debug')
            ->with('无远程规则id，跳过', $this->anything());

        $this->workService->expects($this->never())->method('request');

        $this->listener->postRemove($rule);
    }

    public function test_postRemove_withException_logsError(): void
    {
        $agent = $this->createMock(Agent::class);
        $rule = $this->createMock(InterceptRule::class);
        
        $rule->expects($this->exactly(2))->method('getRuleId')->willReturn('rule_123');
        $rule->expects($this->once())->method('getAgent')->willReturn($agent);

        $exception = new \RuntimeException('API Error');
        $this->workService->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('从远程删除敏感词规则ID时发生异常', ['exception' => $exception]);

        $this->listener->postRemove($rule);
    }

    public function test_preUpdate_calculatesUserAndDepartmentDifferences(): void
    {
        $agent = $this->createMock(Agent::class);
        $rule = $this->createMock(InterceptRule::class);
        
        $rule->expects($this->once())->method('isSync')->willReturn(true);
        $rule->expects($this->exactly(3))->method('getRuleId')->willReturn('rule_123');
        $rule->expects($this->exactly(2))->method('getAgent')->willReturn($agent);
        $rule->expects($this->once())->method('getName')->willReturn('Test Rule');
        $rule->expects($this->once())->method('getWordList')->willReturn(['word1']);
        $rule->expects($this->once())->method('getInterceptType')->willReturn(InterceptType::NOTICE);
        $rule->expects($this->once())->method('getSemanticsList')->willReturn([]);
        
        // Local has: user1, user2
        // Remote has: user2, user3
        // Add: user1, Remove: user3
        $rule->expects($this->exactly(2))->method('getApplicableUserList')->willReturn(['user1', 'user2']);
        
        // Local has: dept1
        // Remote has: dept1, dept2
        // Add: [], Remove: dept2
        $rule->expects($this->exactly(2))->method('getApplicableDepartmentList')->willReturn(['dept1']);

        $detailResponse = [
            'rule' => [
                'applicable_range' => [
                    'user_list' => ['user2', 'user3'],
                    'department_list' => ['dept1', 'dept2']
                ]
            ]
        ];

        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->willReturnCallback(function ($request) use ($detailResponse) {
                if ($request instanceof GetInterceptRuleDetailRequest) {
                    return $detailResponse;
                }
                
                if ($request instanceof UpdateInterceptRuleRequest) {
                    $this->assertEquals(['user1'], array_values($request->getAddApplicableUserList()));
                    $this->assertEquals(['user3'], array_values($request->getRemoveApplicableUserList()));
                    $this->assertEquals([], array_values($request->getAddApplicableDepartmentList()));
                    $this->assertEquals(['dept2'], array_values($request->getRemoveApplicableDepartmentList()));
                }
                
                return [];
            });

        $this->listener->preUpdate($rule);
    }
}