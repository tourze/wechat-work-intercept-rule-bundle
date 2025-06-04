<?php

namespace WechatWorkInterceptRuleBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkInterceptRuleBundle\Command\SyncInterceptRuleCommand;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;
use WechatWorkInterceptRuleBundle\Repository\InterceptRuleRepository;

class SyncInterceptRuleCommandTest extends TestCase
{
    private AgentRepository&MockObject $agentRepository;
    private InterceptRuleRepository&MockObject $ruleRepository;
    private WorkService&MockObject $workService;
    private DoctrineService&MockObject $doctrineService;
    private SyncInterceptRuleCommand $command;
    private OutputInterface&MockObject $output;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->ruleRepository = $this->createMock(InterceptRuleRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->doctrineService = $this->createMock(DoctrineService::class);
        $this->output = $this->createMock(OutputInterface::class);

        $this->command = new SyncInterceptRuleCommand(
            $this->agentRepository,
            $this->ruleRepository,
            $this->workService,
            $this->doctrineService
        );
    }

    public function testExecuteWithNoAgents(): void
    {
        // 准备数据
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $input = $this->createMock(InputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $this->output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithAgentButNoRuleList(): void
    {
        // 准备数据
        $corp = new Corp();
        $corp->setName('测试企业');
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([]); // 没有 rule_list 字段

        $this->output->expects($this->once())
            ->method('writeln')
            ->with('[测试企业] 拉取敏感词列表失败');

        $input = $this->createMock(InputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $this->output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithNewInterceptRule(): void
    {
        // 准备数据
        $corp = new Corp();
        $corp->setName('测试企业');
        $agent = new Agent();
        $agent->setCorp($corp);

        $ruleListResponse = [
            'rule_list' => [
                [
                    'rule_id' => 'rule123',
                    'rule_name' => '敏感词规则1',
                    'create_time' => 1640995200 // 2022-01-01 00:00:00
                ]
            ]
        ];

        $ruleDetailResponse = [
            'rule' => [
                'word_list' => ['敏感词1', '敏感词2'],
                'intercept_type' => 1,
                'applicable_range' => [
                    'user_list' => ['user1', 'user2'],
                    'department_list' => [1, 2]
                ]
            ]
        ];

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                $ruleListResponse, // GetInterceptRuleListRequest
                $ruleDetailResponse // GetInterceptRuleDetailRequest
            );

        $this->ruleRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'ruleId' => 'rule123'
            ])
            ->willReturn(null);

        $this->doctrineService->expects($this->once())
            ->method('directInsert')
            ->with($this->callback(function (InterceptRule $rule) use ($corp, $agent) {
                return $rule->getCorp() === $corp
                    && $rule->getAgent() === $agent
                    && $rule->getRuleId() === 'rule123'
                    && $rule->getName() === '敏感词规则1'
                    && $rule->getWordList() === ['敏感词1', '敏感词2']
                    && $rule->getInterceptType() === InterceptType::tryFrom(1)
                    && $rule->getApplicableUserList() === ['user1', 'user2']
                    && $rule->getApplicableDepartmentList() === [1, 2]
                    && $rule->isSync() === true
                    && $rule->getCreateTime()->format('Y-m-d H:i:s') === '2022-01-01 00:00:00';
            }));

        $input = $this->createMock(InputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $this->output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithExistingInterceptRule(): void
    {
        // 准备数据
        $corp = new Corp();
        $corp->setName('测试企业');
        $agent = new Agent();
        $agent->setCorp($corp);

        $existingRule = new InterceptRule();
        $existingRule->setRuleId('rule123');

        $ruleListResponse = [
            'rule_list' => [
                [
                    'rule_id' => 'rule123',
                    'rule_name' => '敏感词规则1',
                    'create_time' => 1640995200
                ]
            ]
        ];

        $ruleDetailResponse = [
            'rule' => [
                'word_list' => ['敏感词1'],
                'intercept_type' => 2,
                'applicable_range' => [
                    'user_list' => ['user1'],
                    'department_list' => [1]
                ]
            ]
        ];

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls($ruleListResponse, $ruleDetailResponse);

        $this->ruleRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($existingRule);

        // 已存在的规则不会被插入
        $this->doctrineService->expects($this->never())
            ->method('directInsert');

        $input = $this->createMock(InputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $this->output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithMultipleRules(): void
    {
        // 准备数据
        $corp = new Corp();
        $corp->setName('测试企业');
        $agent = new Agent();
        $agent->setCorp($corp);

        $ruleListResponse = [
            'rule_list' => [
                [
                    'rule_id' => 'rule1',
                    'rule_name' => '规则1',
                    'create_time' => 1640995200
                ],
                [
                    'rule_id' => 'rule2',
                    'rule_name' => '规则2',
                    'create_time' => 1641081600
                ]
            ]
        ];

        $ruleDetailResponse1 = [
            'rule' => [
                'word_list' => ['词1'],
                'intercept_type' => 1,
                'applicable_range' => [
                    'user_list' => ['user1'],
                    'department_list' => [1]
                ]
            ]
        ];

        $ruleDetailResponse2 = [
            'rule' => [
                'word_list' => ['词2'],
                'intercept_type' => 2,
                'applicable_range' => [
                    'user_list' => ['user2'],
                    'department_list' => [2]
                ]
            ]
        ];

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->exactly(3))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                $ruleListResponse,
                $ruleDetailResponse1,
                $ruleDetailResponse2
            );

        $this->ruleRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturn(null);

        $this->doctrineService->expects($this->exactly(2))
            ->method('directInsert');

        $input = $this->createMock(InputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $this->output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithMultipleAgents(): void
    {
        // 准备数据
        $corp1 = new Corp();
        $corp1->setName('企业1');
        $corp2 = new Corp();
        $corp2->setName('企业2');
        
        $agent1 = new Agent();
        $agent1->setCorp($corp1);
        $agent2 = new Agent();
        $agent2->setCorp($corp2);

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent1, $agent2]);

        $this->workService->expects($this->exactly(4))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['rule_list' => [['rule_id' => 'rule1', 'rule_name' => '规则1', 'create_time' => 1640995200]]],
                ['rule' => ['word_list' => ['词1'], 'intercept_type' => 1, 'applicable_range' => ['user_list' => [], 'department_list' => []]]],
                ['rule_list' => [['rule_id' => 'rule2', 'rule_name' => '规则2', 'create_time' => 1641081600]]],
                ['rule' => ['word_list' => ['词2'], 'intercept_type' => 2, 'applicable_range' => ['user_list' => [], 'department_list' => []]]]
            );

        $this->ruleRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturn(null);

        $this->doctrineService->expects($this->exactly(2))
            ->method('directInsert');

        $input = $this->createMock(InputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $this->output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testCommandMetadata(): void
    {
        // 验证命令名称
        $this->assertEquals('wechat-work:sync-intercept-rule', $this->command->getName());
        
        // 验证命令描述
        $this->assertEquals('同步敏感词规则', $this->command->getDescription());
        
        // 验证构造函数依赖
        $reflection = new \ReflectionClass($this->command);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        
        $this->assertCount(4, $parameters);
        $this->assertEquals('agentRepository', $parameters[0]->getName());
        $this->assertEquals('ruleRepository', $parameters[1]->getName());
        $this->assertEquals('workService', $parameters[2]->getName());
        $this->assertEquals('doctrineService', $parameters[3]->getName());
    }
} 