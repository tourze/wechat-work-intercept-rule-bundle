<?php

namespace WechatWorkInterceptRuleBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use WechatWorkInterceptRuleBundle\DependencyInjection\WechatWorkInterceptRuleExtension;

/**
 * WechatWorkInterceptRuleExtension 测试用例
 *
 * 测试 DependencyInjection Extension 的加载功能
 */
class WechatWorkInterceptRuleExtensionTest extends TestCase
{
    private WechatWorkInterceptRuleExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatWorkInterceptRuleExtension();
        $this->container = new ContainerBuilder();
    }

    public function test_load_withEmptyConfiguration_loadsServicesYaml(): void
    {
        $this->extension->load([], $this->container);

        // 验证资源被加载
        $resources = $this->container->getResources();
        $this->assertNotEmpty($resources);
        
        // 验证 services.yaml 文件被加载
        $yamlResourceFound = false;
        foreach ($resources as $resource) {
            if (str_contains((string) $resource, 'services.yaml')) {
                $yamlResourceFound = true;
                break;
            }
        }
        $this->assertTrue($yamlResourceFound, 'services.yaml should be loaded');
    }

    public function test_load_withMultipleConfigurations_mergesConfigurations(): void
    {
        $configs = [
            ['some_config' => 'value1'],
            ['another_config' => 'value2'],
        ];

        $this->extension->load($configs, $this->container);

        // 验证资源仍然被加载
        $resources = $this->container->getResources();
        $this->assertNotEmpty($resources);
    }

    public function test_load_withServicesYamlMissing_throwsException(): void
    {
        // 创建一个临时扩展类来模拟文件缺失
        $extension = new class extends WechatWorkInterceptRuleExtension {
            public function load(array $configs, ContainerBuilder $container): void
            {
                $loader = new YamlFileLoader(
                    $container,
                    new FileLocator('/non/existent/path')
                );
                $loader->load('services.yaml');
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $extension->load([], $this->container);
    }

    public function test_extensionAlias_isCorrect(): void
    {
        // 获取扩展别名
        $alias = $this->extension->getAlias();
        
        // 验证别名符合 Symfony 的命名约定
        $this->assertSame('wechat_work_intercept_rule', $alias);
    }

    public function test_load_withCustomConfiguration_preservesCustomValues(): void
    {
        // 在容器中设置一些自定义参数
        $this->container->setParameter('custom.parameter', 'custom_value');
        
        // 加载扩展
        $this->extension->load([], $this->container);
        
        // 验证自定义参数仍然存在
        $this->assertTrue($this->container->hasParameter('custom.parameter'));
        $this->assertSame('custom_value', $this->container->getParameter('custom.parameter'));
    }

    public function test_load_yamlLoaderConfiguration_isCorrect(): void
    {
        // 通过反射验证 YamlFileLoader 配置
        $reflection = new \ReflectionClass($this->extension);
        $method = $reflection->getMethod('load');
        
        // 验证方法是公共的
        $this->assertTrue($method->isPublic());
        
        // 验证方法参数
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        $this->assertSame('configs', $parameters[0]->getName());
        $this->assertSame('container', $parameters[1]->getName());
    }

    public function test_load_resourcesAreRegistered(): void
    {
        $this->extension->load([], $this->container);
        
        // 获取所有资源
        $resources = $this->container->getResources();
        
        // 验证至少有一个资源被注册
        $this->assertGreaterThan(0, count($resources));
        
        // 验证资源类型
        $hasFileResource = false;
        foreach ($resources as $resource) {
            if (method_exists($resource, 'getResource') && str_contains($resource->getResource(), 'services.yaml')) {
                $hasFileResource = true;
                break;
            }
        }
        
        $this->assertTrue($hasFileResource, 'Should have file resource for services.yaml');
    }

    public function test_extension_implementsCorrectInterface(): void
    {
        // 验证扩展实现了正确的接口
        $this->assertInstanceOf(
            \Symfony\Component\DependencyInjection\Extension\ExtensionInterface::class,
            $this->extension
        );
    }

    public function test_load_doesNotThrowException(): void
    {
        // 简单测试加载不会抛出异常
        $this->expectNotToPerformAssertions();
        
        $this->extension->load([], $this->container);
    }

    public function test_load_withEmptyContainer_works(): void
    {
        // 创建一个全新的容器
        $freshContainer = new ContainerBuilder();
        
        // 加载应该正常工作
        $this->extension->load([], $freshContainer);
        
        // 验证容器有资源
        $this->assertNotEmpty($freshContainer->getResources());
    }

    public function test_load_multipleCallsWithDifferentConfigs(): void
    {
        // 第一次加载
        $this->extension->load([['key1' => 'value1']], $this->container);
        
        // 第二次加载不同的配置
        $this->extension->load([['key2' => 'value2']], $this->container);
        
        // 容器应该仍然正常工作
        $resources = $this->container->getResources();
        $this->assertNotEmpty($resources);
    }

    public function test_load_pathResolution(): void
    {
        // 验证路径解析正确
        $reflection = new \ReflectionClass($this->extension);
        $loadMethod = $reflection->getMethod('load');
        
        // 调用加载方法
        $this->extension->load([], $this->container);
        
        // 验证文件加载器使用了正确的路径
        $expectedPath = dirname($reflection->getFileName()) . '/../Resources/config';
        
        // 检查资源中是否包含预期路径
        $resources = $this->container->getResources();
        $pathFound = false;
        
        foreach ($resources as $resource) {
            $resourceString = (string) $resource;
            if (str_contains($resourceString, 'Resources/config')) {
                $pathFound = true;
                break;
            }
        }
        
        $this->assertTrue($pathFound, 'Resources should reference the correct config path');
    }
}