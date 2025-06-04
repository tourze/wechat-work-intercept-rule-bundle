<?php

namespace WechatWorkInterceptRuleBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleListRequest;

/**
 * GetInterceptRuleListRequest 测试
 */
class GetInterceptRuleListRequestTest extends TestCase
{
    private GetInterceptRuleListRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetInterceptRuleListRequest();
    }

    public function test_inheritance(): void
    {
        // 测试继承关系
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }

    public function test_traits(): void
    {
        // 测试使用的trait
        $traits = class_uses($this->request);
        $this->assertContains(AgentAware::class, $traits);
    }

    public function test_getRequestPath(): void
    {
        // 测试请求路径
        $expectedPath = '/cgi-bin/externalcontact/get_intercept_rule_list';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function test_getRequestMethod(): void
    {
        // 测试请求方法
        $expectedMethod = 'GET';
        $this->assertSame($expectedMethod, $this->request->getRequestMethod());
    }

    public function test_agent_methods(): void
    {
        // 测试AgentAware trait的方法存在性
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
        $this->assertTrue(is_callable([$this->request, 'setAgent']));
        $this->assertTrue(is_callable([$this->request, 'getAgent']));
    }

    public function test_getRequestOptions_emptyArray(): void
    {
        // 测试获取请求选项返回空数组
        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertEmpty($options);
        $this->assertCount(0, $options);
    }

    public function test_requestOptions_consistency(): void
    {
        // 测试请求选项的一致性
        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        $this->assertEquals($options1, $options2);
        $this->assertSame($options1, $options2);
    }

    public function test_businessScenario_getAllRules(): void
    {
        // 测试业务场景：获取所有敏感词规则列表
        $options = $this->request->getRequestOptions();
        $path = $this->request->getRequestPath();
        $method = $this->request->getRequestMethod();

        $this->assertEmpty($options);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $path);
        $this->assertSame('GET', $method);
    }

    public function test_businessScenario_adminReview(): void
    {
        // 测试业务场景：管理员审查所有规则
        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertEmpty($options);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $this->request->getRequestPath());
    }

    public function test_businessScenario_ruleManagement(): void
    {
        // 测试业务场景：规则管理界面获取列表
        $path = $this->request->getRequestPath();
        $method = $this->request->getRequestMethod();
        $options = $this->request->getRequestOptions();

        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $path);
        $this->assertSame('GET', $method);
        $this->assertEmpty($options);
    }

    public function test_businessScenario_complianceAudit(): void
    {
        // 测试业务场景：合规审计获取规则列表
        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertCount(0, $options);
        $this->assertSame('GET', $this->request->getRequestMethod());
    }

    public function test_businessScenario_systemMonitoring(): void
    {
        // 测试业务场景：系统监控获取规则状态
        $path = $this->request->getRequestPath();
        $options = $this->request->getRequestOptions();

        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $path);
        $this->assertEmpty($options);
    }

    public function test_businessScenario_reportGeneration(): void
    {
        // 测试业务场景：生成规则报告
        $options = $this->request->getRequestOptions();
        $method = $this->request->getRequestMethod();

        $this->assertEmpty($options);
        $this->assertSame('GET', $method);
    }

    public function test_requestPath_immutable(): void
    {
        // 测试请求路径不可变
        $path1 = $this->request->getRequestPath();
        // 尝试修改（虽然没有setter方法）
        $path2 = $this->request->getRequestPath();

        $this->assertSame($path1, $path2);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule_list', $path1);
    }

    public function test_requestMethod_immutable(): void
    {
        // 测试请求方法不可变
        $method1 = $this->request->getRequestMethod();
        $method2 = $this->request->getRequestMethod();

        $this->assertSame($method1, $method2);
        $this->assertSame('GET', $method1);
    }

    public function test_requestOptions_alwaysEmpty(): void
    {
        // 测试请求选项始终为空
        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();
        $options3 = $this->request->getRequestOptions();

        $this->assertEmpty($options1);
        $this->assertEmpty($options2);
        $this->assertEmpty($options3);
        $this->assertEquals([], $options1);
        $this->assertEquals([], $options2);
        $this->assertEquals([], $options3);
    }

    public function test_noParametersRequired(): void
    {
        // 测试不需要任何参数
        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertEmpty($options);
        $this->assertArrayNotHasKey('json', $options);
        $this->assertArrayNotHasKey('query', $options);
        $this->assertArrayNotHasKey('form_params', $options);
        $this->assertArrayNotHasKey('body', $options);
    }

    public function test_httpGetMethod(): void
    {
        // 测试HTTP GET方法
        $method = $this->request->getRequestMethod();

        $this->assertIsString($method);
        $this->assertSame('GET', $method);
        $this->assertNotSame('POST', $method);
        $this->assertNotSame('PUT', $method);
        $this->assertNotSame('DELETE', $method);
    }

    public function test_apiEndpointStructure(): void
    {
        // 测试API端点结构
        $path = $this->request->getRequestPath();

        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('get_intercept_rule_list', $path);
        $this->assertStringEndsWith('get_intercept_rule_list', $path);
    }

    public function test_idempotency(): void
    {
        // 测试幂等性
        $path1 = $this->request->getRequestPath();
        $method1 = $this->request->getRequestMethod();
        $options1 = $this->request->getRequestOptions();

        $path2 = $this->request->getRequestPath();
        $method2 = $this->request->getRequestMethod();
        $options2 = $this->request->getRequestOptions();

        $this->assertSame($path1, $path2);
        $this->assertSame($method1, $method2);
        $this->assertEquals($options1, $options2);
    }

    public function test_getMethodWithoutBody(): void
    {
        // 测试GET方法不包含请求体
        $options = $this->request->getRequestOptions();
        $method = $this->request->getRequestMethod();

        $this->assertSame('GET', $method);
        $this->assertEmpty($options);
        $this->assertArrayNotHasKey('json', $options);
        $this->assertArrayNotHasKey('body', $options);
    }

    public function test_staticBehavior(): void
    {
        // 测试静态行为（所有实例都相同）
        $request1 = new GetInterceptRuleListRequest();
        $request2 = new GetInterceptRuleListRequest();

        $this->assertSame($request1->getRequestPath(), $request2->getRequestPath());
        $this->assertSame($request1->getRequestMethod(), $request2->getRequestMethod());
        $this->assertEquals($request1->getRequestOptions(), $request2->getRequestOptions());
    }

    public function test_inheritanceChain(): void
    {
        // 测试继承链
        $this->assertInstanceOf(\HttpClientBundle\Request\ApiRequest::class, $this->request);
        $this->assertInstanceOf(\WechatWorkInterceptRuleBundle\Request\GetInterceptRuleListRequest::class, $this->request);
    }

    public function test_traitUsage(): void
    {
        // 测试trait使用
        $uses = class_uses($this->request);
        $this->assertContains(AgentAware::class, $uses);
        $this->assertCount(1, $uses); // 只使用一个trait
    }

    public function test_noStateChanges(): void
    {
        // 测试无状态变化
        $initialPath = $this->request->getRequestPath();
        $initialMethod = $this->request->getRequestMethod();
        $initialOptions = $this->request->getRequestOptions();

        // 多次调用
        for ($i = 0; $i < 10; $i++) {
            $this->assertSame($initialPath, $this->request->getRequestPath());
            $this->assertSame($initialMethod, $this->request->getRequestMethod());
            $this->assertEquals($initialOptions, $this->request->getRequestOptions());
        }
    }

    public function test_methodReturnTypes(): void
    {
        // 测试方法返回类型
        $path = $this->request->getRequestPath();
        $method = $this->request->getRequestMethod();
        $options = $this->request->getRequestOptions();

        $this->assertIsString($path);
        $this->assertIsString($method);
        $this->assertIsArray($options);
    }

    public function test_emptyOptionsArray(): void
    {
        // 测试空选项数组的特性
        $options = $this->request->getRequestOptions();

        $this->assertSame([], $options);
        $this->assertCount(0, $options);
        $this->assertEmpty($options);
        $this->assertTrue(is_array($options));
        $this->assertFalse(isset($options[0]));
        $this->assertArrayNotHasKey('any_key', $options);
    }
} 