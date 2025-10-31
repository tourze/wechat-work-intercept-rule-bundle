<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkInterceptRuleBundle\Command\SyncInterceptRuleCommand;

/**
 * SyncInterceptRuleCommand 集成测试
 *
 * @internal
 */
#[CoversClass(SyncInterceptRuleCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncInterceptRuleCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // Mock AgentRepository 返回空数组，避免调用WorkService
        $agentRepository = $this->createMock(AgentRepository::class);
        $agentRepository->method('findAll')->willReturn([]);

        // 将Mock注入到容器
        self::getContainer()->set(AgentRepository::class, $agentRepository);
    }

    protected function getCommandTester(): CommandTester
    {
        // 创建并返回 CommandTester
        $command = self::getContainer()->get(SyncInterceptRuleCommand::class);
        self::assertInstanceOf(SyncInterceptRuleCommand::class, $command);

        return new CommandTester($command);
    }

    public function testExecuteCommandSuccessfully(): void
    {
        // 测试命令可以成功执行（由于没有agent，会直接返回成功）
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $this->assertSame(0, $commandTester->getStatusCode());
    }

    public function testCommandServiceIsRegistered(): void
    {
        $command = self::getContainer()->get(SyncInterceptRuleCommand::class);

        $this->assertInstanceOf(SyncInterceptRuleCommand::class, $command);
        $this->assertSame('wechat-work:sync-intercept-rule', $command->getName());
    }
}
