<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleListRequest;

/**
 * GetInterceptRuleListRequest 测试
 *
 * @internal
 */
#[CoversClass(GetInterceptRuleListRequest::class)]
final class GetInterceptRuleListRequestTest extends RequestTestCase
{
    private GetInterceptRuleListRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new GetInterceptRuleListRequest();
    }

    public function testInheritance(): void
    {
        // 测试继承关系
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }

    public function testTraits(): void
    {
        // 测试使用的trait
        $traits = class_uses($this->request);
        $this->assertContains(AgentAware::class, $traits);
    }

    public function testGetRequestPath(): void
    {
        // 测试请求路径
        $expectedPath = '/cgi-bin/externalcontact/get_intercept_rule_list';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        // 测试请求方法
        $expectedMethod = 'GET';
        $this->assertSame($expectedMethod, $this->request->getRequestMethod());
    }

    public function testAgentMethods(): void
    {
        // 测试AgentAware trait的方法存在
        // 如果方法不存在，测试会在调用时失败
        $agent = $this->createMock(AgentInterface::class);
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());
    }

    public function testGetRequestOptionsEmptyArray(): void
    {
        // 测试获取请求选项返回空数组
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertEmpty($options);
        $this->assertCount(0, $options);
    }

    public function testRequestOptionsConsistency(): void
    {
        // 测试请求选项的一致性
        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        $this->assertNotNull($options1);
        $this->assertNotNull($options2);
        $this->assertEquals($options1, $options2);
        $this->assertSame($options1, $options2);
    }

    public function testBusinessScenarioGetAllRules(): void
    {
        // 测试业务场景：获取所有敏感词规则列表
        $options = $this->request->getRequestOptions();
        $path = $this->request->getRequestPath();
        $method = $this->request->getRequestMethod();

        $this->assertNotNull($options);
        $this->assertEmpty($options);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $path);
        $this->assertSame('GET', $method);
    }

    public function testBusinessScenarioAdminReview(): void
    {
        // 测试业务场景：管理员审查所有规则
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertEmpty($options);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $this->request->getRequestPath());
    }

    public function testBusinessScenarioRuleManagement(): void
    {
        // 测试业务场景：规则管理界面获取列表
        $path = $this->request->getRequestPath();
        $method = $this->request->getRequestMethod();
        $options = $this->request->getRequestOptions();

        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $path);
        $this->assertSame('GET', $method);
        $this->assertEmpty($options);
    }

    public function testBusinessScenarioComplianceAudit(): void
    {
        // 测试业务场景：合规审计获取规则列表
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertCount(0, $options);
        $this->assertSame('GET', $this->request->getRequestMethod());
    }

    public function testBusinessScenarioSystemMonitoring(): void
    {
        // 测试业务场景：系统监控获取规则状态
        $path = $this->request->getRequestPath();
        $options = $this->request->getRequestOptions();

        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $path);
        $this->assertEmpty($options);
    }

    public function testBusinessScenarioReportGeneration(): void
    {
        // 测试业务场景：生成规则报告
        $options = $this->request->getRequestOptions();
        $method = $this->request->getRequestMethod();

        $this->assertEmpty($options);
        $this->assertSame('GET', $method);
    }

    public function testRequestPathImmutable(): void
    {
        // 测试请求路径不可变
        $path1 = $this->request->getRequestPath();
        // 尝试修改（虽然没有setter方法）
        $path2 = $this->request->getRequestPath();

        $this->assertSame($path1, $path2);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $path1);
    }

    public function testRequestMethodImmutable(): void
    {
        // 测试请求方法不可变
        $method1 = $this->request->getRequestMethod();
        $method2 = $this->request->getRequestMethod();

        $this->assertSame($method1, $method2);
        $this->assertSame('GET', $method1);
    }

    public function testRequestOptionsAlwaysEmpty(): void
    {
        // 测试请求选项始终为空
        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();
        $options3 = $this->request->getRequestOptions();

        $this->assertNotNull($options1);
        $this->assertNotNull($options2);
        $this->assertNotNull($options3);
        $this->assertEmpty($options1);
        $this->assertEmpty($options2);
        $this->assertEmpty($options3);
        $this->assertEquals([], $options1);
        $this->assertEquals([], $options2);
        $this->assertEquals([], $options3);
    }

    public function testNoParametersRequired(): void
    {
        // 测试不需要任何参数
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertEmpty($options);
        $this->assertArrayNotHasKey('json', $options);
        $this->assertArrayNotHasKey('query', $options);
        $this->assertArrayNotHasKey('form_params', $options);
        $this->assertArrayNotHasKey('body', $options);
    }

    public function testHttpGetMethod(): void
    {
        // 测试HTTP GET方法
        $method = $this->request->getRequestMethod();
        $this->assertSame('GET', $method);
    }

    public function testApiEndpointStructure(): void
    {
        // 测试API端点结构
        $path = $this->request->getRequestPath();

        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('get_intercept_rule_list', $path);
        $this->assertStringEndsWith('get_intercept_rule_list', $path);
    }

    public function testIdempotency(): void
    {
        // 测试幂等性
        $path1 = $this->request->getRequestPath();
        $method1 = $this->request->getRequestMethod();
        $options1 = $this->request->getRequestOptions();

        $path2 = $this->request->getRequestPath();
        $method2 = $this->request->getRequestMethod();
        $options2 = $this->request->getRequestOptions();

        $this->assertNotNull($options1);
        $this->assertNotNull($options2);
        $this->assertSame($path1, $path2);
        $this->assertSame($method1, $method2);
        $this->assertEquals($options1, $options2);
    }

    public function testGetMethodWithoutBody(): void
    {
        // 测试GET方法不包含请求体
        $options = $this->request->getRequestOptions();
        $method = $this->request->getRequestMethod();

        $this->assertNotNull($options);
        $this->assertSame('GET', $method);
        $this->assertEmpty($options);
        $this->assertArrayNotHasKey('json', $options);
        $this->assertArrayNotHasKey('body', $options);
    }

    public function testStaticBehavior(): void
    {
        // 测试静态行为（所有实例都相同）
        $request1 = new GetInterceptRuleListRequest();
        $request2 = new GetInterceptRuleListRequest();

        $this->assertSame($request1->getRequestPath(), $request2->getRequestPath());
        $this->assertSame($request1->getRequestMethod(), $request2->getRequestMethod());
        $options1 = $request1->getRequestOptions();
        $options2 = $request2->getRequestOptions();
        $this->assertNotNull($options1);
        $this->assertNotNull($options2);
        $this->assertEquals($options1, $options2);
    }

    public function testInheritanceChain(): void
    {
        // 测试继承链
        $this->assertInstanceOf(ApiRequest::class, $this->request);
        $this->assertInstanceOf(GetInterceptRuleListRequest::class, $this->request);
    }

    public function testTraitUsage(): void
    {
        // 测试trait使用
        $uses = class_uses($this->request);
        $this->assertContains(AgentAware::class, $uses);
        $this->assertCount(1, $uses); // 只使用一个trait
    }

    public function testNoStateChanges(): void
    {
        // 测试无状态变化
        $initialPath = $this->request->getRequestPath();
        $initialMethod = $this->request->getRequestMethod();
        $initialOptions = $this->request->getRequestOptions();
        $this->assertNotNull($initialOptions);

        // 多次调用
        for ($i = 0; $i < 10; ++$i) {
            $this->assertSame($initialPath, $this->request->getRequestPath());
            $this->assertSame($initialMethod, $this->request->getRequestMethod());
            $currentOptions = $this->request->getRequestOptions();
            $this->assertNotNull($currentOptions);
            $this->assertEquals($initialOptions, $currentOptions);
        }
    }

    public function testMethodReturnTypes(): void
    {
        // 测试方法返回值的内容
        $path = $this->request->getRequestPath();
        $method = $this->request->getRequestMethod();
        $options = $this->request->getRequestOptions();

        // 验证返回值的具体内容而不是类型
        $this->assertNotNull($options);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $path);
        $this->assertSame('GET', $method);
        $this->assertSame([], $options);
    }

    public function testEmptyOptionsArray(): void
    {
        // 测试空选项数组的特性
        $options = $this->request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertSame([], $options);
        $this->assertCount(0, $options);
        // 保留有意义的检查
        $this->assertArrayNotHasKey('any_key', $options);
    }
}
