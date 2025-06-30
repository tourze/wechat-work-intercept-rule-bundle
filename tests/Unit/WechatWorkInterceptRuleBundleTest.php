<?php

namespace WechatWorkInterceptRuleBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WechatWorkInterceptRuleBundle\WechatWorkInterceptRuleBundle;

/**
 * WechatWorkInterceptRuleBundle 测试用例
 *
 * 测试 Bundle 类的基本功能
 */
class WechatWorkInterceptRuleBundleTest extends TestCase
{
    private WechatWorkInterceptRuleBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new WechatWorkInterceptRuleBundle();
    }

    public function test_bundleExtendsSymfonyBundle(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function test_bundleCanBeInstantiated(): void
    {
        $bundle = new WechatWorkInterceptRuleBundle();
        $this->assertNotNull($bundle);
    }

    public function test_bundleGetNameReturnsCorrectName(): void
    {
        $this->assertEquals('WechatWorkInterceptRuleBundle', $this->bundle->getName());
    }

    public function test_bundleGetPath(): void
    {
        $path = $this->bundle->getPath();
        $this->assertStringContainsString('wechat-work-intercept-rule-bundle', $path);
        $this->assertDirectoryExists($path);
    }

    public function test_bundleGetNamespace(): void
    {
        $namespace = $this->bundle->getNamespace();
        $this->assertEquals('WechatWorkInterceptRuleBundle', $namespace);
    }

    public function test_bundleGetContainerExtension(): void
    {
        // Bundle 类会自动查找对应的 Extension 类
        $extension = $this->bundle->getContainerExtension();
        $this->assertNotNull($extension);
        $this->assertEquals('wechat_work_intercept_rule', $extension->getAlias());
    }

    public function test_bundleBoot(): void
    {
        // 测试 boot 方法不会抛出异常
        $this->expectNotToPerformAssertions();
        $this->bundle->boot();
    }

    public function test_bundleShutdown(): void
    {
        // 测试 shutdown 方法不会抛出异常
        $this->expectNotToPerformAssertions();
        $this->bundle->shutdown();
    }

    public function test_bundleBuild(): void
    {
        $container = $this->createMock(\Symfony\Component\DependencyInjection\ContainerBuilder::class);
        
        // 测试 build 方法不会抛出异常
        $this->expectNotToPerformAssertions();
        $this->bundle->build($container);
    }


    public function test_bundleReflection(): void
    {
        $reflection = new \ReflectionClass($this->bundle);
        
        // 验证类的基本属性
        $this->assertTrue($reflection->isInstantiable());
        $this->assertFalse($reflection->isAbstract());
        $this->assertFalse($reflection->isFinal());
        $this->assertFalse($reflection->isInterface());
        $this->assertFalse($reflection->isTrait());
    }

    public function test_bundleClassLocation(): void
    {
        $reflection = new \ReflectionClass($this->bundle);
        $filename = $reflection->getFileName();
        
        $this->assertStringContainsString('WechatWorkInterceptRuleBundle.php', $filename);
        $this->assertFileExists($filename);
    }

    public function test_bundleConstants(): void
    {
        $reflection = new \ReflectionClass($this->bundle);
        $constants = $reflection->getConstants();
        
        // Bundle 基类可能没有常量，但测试不应失败
        $this->assertIsArray($constants);
    }

    public function test_bundleMethods(): void
    {
        $reflection = new \ReflectionClass($this->bundle);
        
        // 验证继承的方法存在
        $this->assertTrue($reflection->hasMethod('getName'));
        $this->assertTrue($reflection->hasMethod('getNamespace'));
        $this->assertTrue($reflection->hasMethod('getPath'));
        $this->assertTrue($reflection->hasMethod('getContainerExtension'));
        $this->assertTrue($reflection->hasMethod('boot'));
        $this->assertTrue($reflection->hasMethod('shutdown'));
        $this->assertTrue($reflection->hasMethod('build'));
    }

    public function test_bundleProperties(): void
    {
        $reflection = new \ReflectionClass($this->bundle);
        $properties = $reflection->getProperties();
        
        // Bundle 可能有继承的属性
        $this->assertIsArray($properties);
    }

    public function test_bundleCanBeSerializedAndDeserialized(): void
    {
        $serialized = serialize($this->bundle);
        $unserialized = unserialize($serialized);
        
        $this->assertInstanceOf(WechatWorkInterceptRuleBundle::class, $unserialized);
        $this->assertEquals($this->bundle->getName(), $unserialized->getName());
    }
}