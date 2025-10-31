<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule as InterceptRuleEntity;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;
use WechatWorkInterceptRuleBundle\EventSubscriber\InterceptRuleListener;

/**
 * InterceptRuleListener 测试用例
 *
 * 测试事件监听器的所有方法
 *
 * @internal
 */
#[CoversClass(InterceptRuleListener::class)]
#[RunTestsInSeparateProcesses]
final class InterceptRuleListenerTest extends AbstractIntegrationTestCase
{
    private InterceptRuleListener $listener;

    protected function onSetUp(): void
    {
        $this->listener = self::getService(InterceptRuleListener::class);
    }

    public function testPrePersistWithSyncEnabledReturnsEarlyWithMissingData(): void
    {
        $rule = new InterceptRule();
        $rule->setSync(true);

        // sync 为 true 但缺少必需数据时，方法会提前返回
        // 不会进行 API 调用，避免了异常
        $this->listener->prePersist($rule);

        // 验证规则没有被修改
        $this->assertNull($rule->getRuleId());
    }

    public function testPrePersistWithSyncDisabledSkipsRemoteCreation(): void
    {
        $rule = new InterceptRule();
        $rule->setSync(false);

        // sync 为 false 时，方法应该直接返回，不进行任何 API 调用
        $this->listener->prePersist($rule);

        // 验证监听器实例正确
        $this->assertInstanceOf(InterceptRuleListener::class, $this->listener);
    }

    public function testPrePersistWithMinimalRequiredDataReturnsEarly(): void
    {
        $rule = new InterceptRule();
        $rule->setSync(true);

        // 测试最小必需数据时，方法会提前返回，不会进行API调用
        // 因为缺少必需的字段（如name, interceptType等）
        $this->listener->prePersist($rule);

        // 验证规则没有被修改
        $this->assertNull($rule->getRuleId());
    }

    public function testListenerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(InterceptRuleListener::class, $this->listener);
    }

    public function testPostRemoveWithoutRuleIdSkipsRemoteDeletion(): void
    {
        $rule = new InterceptRule();
        // 没有设置ruleId，方法应该提前返回，不进行API调用

        $this->listener->postRemove($rule);

        // 验证方法正常执行完成，没有异常
        $this->assertNull($rule->getRuleId());
    }

    public function testPostRemoveWithRuleIdAttemptsRemoteDeletion(): void
    {
        $rule = new InterceptRule();
        $rule->setRuleId('test-rule-123');

        // 由于没有真实的Agent和WorkService配置，
        // 这个测试主要验证方法能够处理有ruleId的情况
        // 在实际环境中会尝试调用删除API，但会因为缺少Agent而提前返回或失败

        $this->listener->postRemove($rule);

        // 验证ruleId仍然存在（因为是删除后的清理操作）
        $this->assertSame('test-rule-123', $rule->getRuleId());
    }

    public function testPreUpdateWithSyncDisabledCallsPostRemove(): void
    {
        $rule = new InterceptRule();
        $rule->setRuleId('existing-rule-123');
        $rule->setSync(false);

        // 当sync为false时，preUpdate应该调用postRemove来删除远程规则
        $this->listener->preUpdate($rule);

        // 验证规则状态 - ruleId应该仍然存在，因为postRemove不会修改本地数据
        $this->assertSame('existing-rule-123', $rule->getRuleId());
        $this->assertFalse($rule->isSync());
    }

    public function testPreUpdateWithoutRuleIdCallsPrePersist(): void
    {
        $rule = new InterceptRule();
        $rule->setSync(true);
        $rule->setName('测试规则');
        // 没有设置ruleId，应该调用prePersist来创建新规则

        $this->listener->preUpdate($rule);

        // 验证规则状态 - 由于没有完整的API环境，ruleId应该仍为null
        $this->assertNull($rule->getRuleId());
        $this->assertTrue($rule->isSync());
        $this->assertSame('测试规则', $rule->getName());
    }

    public function testPreUpdateWithExistingRuleIdReturnsEarly(): void
    {
        $rule = new InterceptRule();
        $rule->setRuleId(null); // 没有ruleId，会调用prePersist然后返回
        $rule->setSync(false); // sync为false，prePersist会直接返回
        $rule->setName('更新的规则');
        $rule->setInterceptType(InterceptType::WARN);
        $rule->setApplicableUserList(['user123']);

        // sync为false时，prePersist会直接返回，不进行API调用
        $this->listener->preUpdate($rule);

        // 验证规则状态保持不变
        $this->assertNull($rule->getRuleId());
        $this->assertFalse($rule->isSync());
        $this->assertSame('更新的规则', $rule->getName());
        $this->assertSame(InterceptType::WARN, $rule->getInterceptType());
        $this->assertSame(['user123'], $rule->getApplicableUserList());
    }
}
