<?php

namespace WechatWorkInterceptRuleBundle\Tests\Command;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\DoctrineDirectInsertBundle\Service\DirectInsertService;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkInterceptRuleBundle\Command\SyncInterceptRuleCommand;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;
use WechatWorkInterceptRuleBundle\Repository\InterceptRuleRepository;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleDetailRequest;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleListRequest;

/**
 * SyncInterceptRuleCommand 测试用例
 *
 * 测试同步敏感词规则命令的功能
 */
class SyncInterceptRuleCommandTest extends TestCase
{
    private SyncInterceptRuleCommand $command;
    private CommandTester $commandTester;
    
    /** @var AgentRepository&MockObject */
    private AgentRepository $agentRepository;
    
    /** @var InterceptRuleRepository&MockObject */
    private InterceptRuleRepository $ruleRepository;
    
    /** @var WorkService&MockObject */
    private WorkService $workService;
    
    /** @var DirectInsertService&MockObject */
    private DirectInsertService $directInsertService;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->ruleRepository = $this->createMock(InterceptRuleRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->directInsertService = $this->createMock(DirectInsertService::class);

        $this->command = new SyncInterceptRuleCommand(
            $this->agentRepository,
            $this->ruleRepository,
            $this->workService,
            $this->directInsertService
        );

        $application = new Application();
        $application->add($this->command);

        $command = $application->find(SyncInterceptRuleCommand::NAME);
        $this->commandTester = new CommandTester($command);
    }

    public function test_execute_withNoAgents_returnsSuccess(): void
    {
        $this->agentRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->workService
            ->expects($this->never())
            ->method('request');

        $this->directInsertService
            ->expects($this->never())
            ->method('directInsert');

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function test_execute_withFailedRuleListRequest_showsErrorMessage(): void
    {
        /** @var Corp&MockObject $corp */
        $corp = $this->createMock(Corp::class);
        $corp->method('getName')->willReturn('测试企业');

        /** @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->method('getCorp')->willReturn($corp);

        $this->agentRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService
            ->expects($this->once())
            ->method('request')
            ->with($this->isInstanceOf(GetInterceptRuleListRequest::class))
            ->willReturn(['errcode' => 1, 'errmsg' => 'access denied']);

        $this->directInsertService
            ->expects($this->never())
            ->method('directInsert');

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('[测试企业] 拉取敏感词列表失败', $output);
        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function test_execute_withNewRule_insertsRule(): void
    {
        /** @var Corp&MockObject $corp */
        $corp = $this->createMock(Corp::class);
        $corp->method('getName')->willReturn('测试企业');

        /** @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->method('getCorp')->willReturn($corp);

        $this->agentRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $ruleListResponse = [
            'rule_list' => [
                [
                    'rule_id' => 'test_rule_123',
                    'rule_name' => '测试规则',
                    'create_time' => 1704877200, // 2024-01-10 10:00:00
                ],
            ],
        ];

        $ruleDetailResponse = [
            'rule' => [
                'word_list' => ['敏感词1', '敏感词2'],
                'intercept_type' => '1',
                'applicable_range' => [
                    'user_list' => ['user1', 'user2'],
                    'department_list' => [1, 2],
                ],
            ],
        ];

        $this->workService
            ->expects($this->exactly(2))
            ->method('request')
            ->willReturnCallback(function ($request) use ($ruleListResponse, $ruleDetailResponse) {
                if ($request instanceof GetInterceptRuleListRequest) {
                    return $ruleListResponse;
                }
                if ($request instanceof GetInterceptRuleDetailRequest) {
                    return $ruleDetailResponse;
                }
                return [];
            });

        $this->ruleRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'ruleId' => 'test_rule_123',
            ])
            ->willReturn(null);

        $this->directInsertService
            ->expects($this->once())
            ->method('directInsert')
            ->with($this->callback(function (InterceptRule $rule) use ($corp, $agent) {
                $this->assertSame($corp, $rule->getCorp());
                $this->assertSame($agent, $rule->getAgent());
                $this->assertSame('test_rule_123', $rule->getRuleId());
                $this->assertSame('测试规则', $rule->getName());
                $this->assertSame(['敏感词1', '敏感词2'], $rule->getWordList());
                $this->assertSame(InterceptType::WARN, $rule->getInterceptType());
                $this->assertSame(['user1', 'user2'], $rule->getApplicableUserList());
                $this->assertSame([1, 2], $rule->getApplicableDepartmentList());
                $this->assertTrue($rule->isSync());
                $this->assertInstanceOf(\DateTimeImmutable::class, $rule->getCreateTime());
                return true;
            }));

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function test_execute_withExistingRule_skipsInsertion(): void
    {
        /** @var Corp&MockObject $corp */
        $corp = $this->createMock(Corp::class);

        /** @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->method('getCorp')->willReturn($corp);

        $this->agentRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $ruleListResponse = [
            'rule_list' => [
                [
                    'rule_id' => 'existing_rule_456',
                    'rule_name' => '已存在规则',
                    'create_time' => 1704877200,
                ],
            ],
        ];

        $ruleDetailResponse = [
            'rule' => [
                'word_list' => ['词1'],
                'intercept_type' => '2',
                'applicable_range' => [
                    'user_list' => [],
                    'department_list' => [],
                ],
            ],
        ];

        $this->workService
            ->expects($this->exactly(2))
            ->method('request')
            ->willReturnCallback(function ($request) use ($ruleListResponse, $ruleDetailResponse) {
                if ($request instanceof GetInterceptRuleListRequest) {
                    return $ruleListResponse;
                }
                if ($request instanceof GetInterceptRuleDetailRequest) {
                    return $ruleDetailResponse;
                }
                return [];
            });

        /** @var InterceptRule&MockObject $existingRule */
        $existingRule = $this->createMock(InterceptRule::class);

        $this->ruleRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'ruleId' => 'existing_rule_456',
            ])
            ->willReturn($existingRule);

        $this->directInsertService
            ->expects($this->never())
            ->method('directInsert');

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function test_execute_withMultipleAgentsAndRules_processesAll(): void
    {
        /** @var Corp&MockObject $corp1 */
        $corp1 = $this->createMock(Corp::class);
        $corp1->method('getName')->willReturn('企业1');

        /** @var Corp&MockObject $corp2 */
        $corp2 = $this->createMock(Corp::class);
        $corp2->method('getName')->willReturn('企业2');

        /** @var Agent&MockObject $agent1 */
        $agent1 = $this->createMock(Agent::class);
        $agent1->method('getCorp')->willReturn($corp1);

        /** @var Agent&MockObject $agent2 */
        $agent2 = $this->createMock(Agent::class);
        $agent2->method('getCorp')->willReturn($corp2);

        $this->agentRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent1, $agent2]);

        $ruleListResponse1 = [
            'rule_list' => [
                ['rule_id' => 'rule1', 'rule_name' => '规则1', 'create_time' => 1704877200],
                ['rule_id' => 'rule2', 'rule_name' => '规则2', 'create_time' => 1704877201],
            ],
        ];

        $ruleListResponse2 = [
            'rule_list' => [
                ['rule_id' => 'rule3', 'rule_name' => '规则3', 'create_time' => 1704877202],
            ],
        ];

        $ruleDetailResponse = [
            'rule' => [
                'word_list' => ['测试词'],
                'intercept_type' => '1',
                'applicable_range' => [
                    'user_list' => [],
                    'department_list' => [],
                ],
            ],
        ];

        $requestCount = 0;
        $this->workService
            ->expects($this->exactly(5)) // 2 list requests + 3 detail requests
            ->method('request')
            ->willReturnCallback(function ($request) use (&$requestCount, $ruleListResponse1, $ruleListResponse2, $ruleDetailResponse, $agent1, $agent2) {
                $requestCount++;
                if ($request instanceof GetInterceptRuleListRequest) {
                    if ($request->getAgent() === $agent1) {
                        return $ruleListResponse1;
                    }
                    if ($request->getAgent() === $agent2) {
                        return $ruleListResponse2;
                    }
                }
                if ($request instanceof GetInterceptRuleDetailRequest) {
                    return $ruleDetailResponse;
                }
                return [];
            });

        $this->ruleRepository
            ->expects($this->exactly(3))
            ->method('findOneBy')
            ->willReturn(null);

        $this->directInsertService
            ->expects($this->exactly(3))
            ->method('directInsert');

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function test_execute_withEmptyRuleList_skipsProcessing(): void
    {
        /** @var Corp&MockObject $corp */
        $corp = $this->createMock(Corp::class);

        /** @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->method('getCorp')->willReturn($corp);

        $this->agentRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $ruleListResponse = [
            'rule_list' => [],
        ];

        $this->workService
            ->expects($this->once())
            ->method('request')
            ->with($this->isInstanceOf(GetInterceptRuleListRequest::class))
            ->willReturn($ruleListResponse);

        $this->ruleRepository
            ->expects($this->never())
            ->method('findOneBy');

        $this->directInsertService
            ->expects($this->never())
            ->method('directInsert');

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function test_execute_withInvalidInterceptType_handlesGracefully(): void
    {
        /** @var Corp&MockObject $corp */
        $corp = $this->createMock(Corp::class);

        /** @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->method('getCorp')->willReturn($corp);

        $this->agentRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $ruleListResponse = [
            'rule_list' => [
                ['rule_id' => 'invalid_type_rule', 'rule_name' => '无效类型规则', 'create_time' => 1704877200],
            ],
        ];

        $ruleDetailResponse = [
            'rule' => [
                'word_list' => ['测试'],
                'intercept_type' => '999', // Invalid type
                'applicable_range' => [
                    'user_list' => [],
                    'department_list' => [],
                ],
            ],
        ];

        $this->workService
            ->expects($this->exactly(2))
            ->method('request')
            ->willReturnCallback(function ($request) use ($ruleListResponse, $ruleDetailResponse) {
                if ($request instanceof GetInterceptRuleListRequest) {
                    return $ruleListResponse;
                }
                if ($request instanceof GetInterceptRuleDetailRequest) {
                    return $ruleDetailResponse;
                }
                return [];
            });

        $this->ruleRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->directInsertService
            ->expects($this->once())
            ->method('directInsert')
            ->with($this->callback(function (InterceptRule $rule) {
                // 验证处理无效类型时的行为
                $this->assertNull($rule->getInterceptType());
                return true;
            }));

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function test_commandAttributes_areConfiguredCorrectly(): void
    {
        $this->assertSame('wechat-work:sync-intercept-rule', SyncInterceptRuleCommand::NAME);
        $this->assertSame(SyncInterceptRuleCommand::NAME, $this->command->getName());
        $this->assertSame('同步敏感词规则', $this->command->getDescription());
    }

    public function test_execute_withTimezoneHandling_usesCorrectTimezone(): void
    {
        /** @var Corp&MockObject $corp */
        $corp = $this->createMock(Corp::class);

        /** @var Agent&MockObject $agent */
        $agent = $this->createMock(Agent::class);
        $agent->method('getCorp')->willReturn($corp);

        $this->agentRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $originalTimezone = date_default_timezone_get();
        date_default_timezone_set('Asia/Shanghai');

        $timestamp = 1704877200; // 2024-01-10 10:00:00 UTC
        $ruleListResponse = [
            'rule_list' => [
                ['rule_id' => 'tz_test', 'rule_name' => '时区测试', 'create_time' => $timestamp],
            ],
        ];

        $ruleDetailResponse = [
            'rule' => [
                'word_list' => ['时区'],
                'intercept_type' => '1',
                'applicable_range' => [
                    'user_list' => [],
                    'department_list' => [],
                ],
            ],
        ];

        $this->workService
            ->expects($this->exactly(2))
            ->method('request')
            ->willReturnCallback(function ($request) use ($ruleListResponse, $ruleDetailResponse) {
                if ($request instanceof GetInterceptRuleListRequest) {
                    return $ruleListResponse;
                }
                if ($request instanceof GetInterceptRuleDetailRequest) {
                    return $ruleDetailResponse;
                }
                return [];
            });

        $this->ruleRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->directInsertService
            ->expects($this->once())
            ->method('directInsert')
            ->with($this->callback(function (InterceptRule $rule) use ($timestamp) {
                $createTime = $rule->getCreateTime();
                $this->assertInstanceOf(\DateTimeImmutable::class, $createTime);
                $this->assertSame($timestamp, $createTime->getTimestamp());
                $this->assertSame('Asia/Shanghai', $createTime->getTimezone()->getName());
                return true;
            }));

        $this->commandTester->execute([]);

        date_default_timezone_set($originalTimezone);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}