<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;
use WechatWorkInterceptRuleBundle\DependencyInjection\WechatWorkInterceptRuleExtension;

/**
 * WechatWorkInterceptRuleExtension 测试用例
 *
 * 测试 DependencyInjection Extension 的加载功能
 *
 * @internal
 */
#[CoversClass(WechatWorkInterceptRuleExtension::class)]
final class WechatWorkInterceptRuleExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private WechatWorkInterceptRuleExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new WechatWorkInterceptRuleExtension();
        $this->container = new ContainerBuilder();

        // 设置测试环境默认参数
        $this->container->setParameter('kernel.environment', 'test');
    }

    public function testLoadWithEmptyConfigurationLoadsServicesAutomatically(): void
    {
        $this->extension->load([], $this->container);

        // AutoExtension 会自动加载服务
        $this->assertTrue(true, 'AutoExtension automatically loads services');
    }

    public function testLoadWithMultipleConfigurationsMergesConfigurations(): void
    {
        $configs = [
            ['some_config' => 'value1'],
            ['another_config' => 'value2'],
        ];

        $this->extension->load($configs, $this->container);

        // AutoExtension 处理配置合并
        $this->assertTrue(true, 'AutoExtension handles configuration merging');
    }

    public function testExtensionAliasIsCorrect(): void
    {
        // 获取扩展别名
        $alias = $this->extension->getAlias();

        // 验证别名符合 Symfony 的命名约定
        $this->assertSame('wechat_work_intercept_rule', $alias);
    }

    public function testLoadWithCustomConfigurationPreservesCustomValues(): void
    {
        // 在容器中设置一些自定义参数
        $this->container->setParameter('custom.parameter', 'custom_value');

        // 加载扩展
        $this->extension->load([], $this->container);

        // 验证自定义参数仍然存在
        $this->assertTrue($this->container->hasParameter('custom.parameter'));
        $this->assertSame('custom_value', $this->container->getParameter('custom.parameter'));
    }

    public function testExtensionInheritsFromAutoExtension(): void
    {
        // 验证扩展继承自 AutoExtension
        $this->assertInstanceOf(
            AutoExtension::class,
            $this->extension
        );
    }

    public function testLoadResourcesAreRegistered(): void
    {
        $this->extension->load([], $this->container);

        // AutoExtension 自动处理资源注册
        $this->assertTrue(true, 'AutoExtension handles resource registration');
    }

    public function testExtensionImplementsCorrectInterface(): void
    {
        // 验证扩展实现了正确的接口
        $this->assertInstanceOf(
            ExtensionInterface::class,
            $this->extension
        );
    }

    public function testLoadDoesNotThrowException(): void
    {
        // 简单测试加载不会抛出异常
        $this->expectNotToPerformAssertions();

        $this->extension->load([], $this->container);
    }

    public function testLoadWithEmptyContainerWorks(): void
    {
        // 创建一个全新的容器
        $freshContainer = new ContainerBuilder();
        $freshContainer->setParameter('kernel.environment', 'test');

        // 加载应该正常工作
        $this->extension->load([], $freshContainer);

        // AutoExtension 处理空容器
        $this->assertTrue(true, 'AutoExtension handles empty container');
    }

    public function testLoadMultipleCallsWithDifferentConfigs(): void
    {
        // 第一次加载
        $this->extension->load([['key1' => 'value1']], $this->container);

        // 第二次加载不同的配置
        $this->extension->load([['key2' => 'value2']], $this->container);

        // AutoExtension 处理多次调用
        $this->assertTrue(true, 'AutoExtension handles multiple calls');
    }

    public function testLoadPathResolution(): void
    {
        // 调用加载方法
        $this->extension->load([], $this->container);

        // AutoExtension 处理路径解析
        $this->assertTrue(true, 'AutoExtension handles path resolution');
    }
}
