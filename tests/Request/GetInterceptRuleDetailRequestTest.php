<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleDetailRequest;

/**
 * GetInterceptRuleDetailRequest 测试
 *
 * @internal
 */
#[CoversClass(GetInterceptRuleDetailRequest::class)]
final class GetInterceptRuleDetailRequestTest extends RequestTestCase
{
    private GetInterceptRuleDetailRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new GetInterceptRuleDetailRequest();
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
        $expectedPath = '/cgi-bin/externalcontact/get_intercept_rule';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function testRuleIdSetterAndGetter(): void
    {
        // 测试规则ID设置和获取
        $ruleId = 'rule_detail_123456';
        $this->request->setRuleId($ruleId);
        $this->assertSame($ruleId, $this->request->getRuleId());
    }

    public function testRuleIdDifferentFormats(): void
    {
        // 测试不同格式的规则ID
        $ruleIds = [
            'rule_detail_123',
            'get_rule_456',
            'RULE_DETAIL_UPPERCASE_789',
            'rule-detail-with-dashes-012',
            'rule.detail.with.dots.345',
            '1234567890',
            'very_long_rule_detail_id_with_many_characters_123456789',
        ];

        foreach ($ruleIds as $ruleId) {
            $this->request->setRuleId($ruleId);
            $this->assertSame($ruleId, $this->request->getRuleId());
        }
    }

    public function testRuleIdSpecialCharacters(): void
    {
        // 测试包含特殊字符的规则ID
        $specialIds = [
            'rule_详情_123',
            'rule_emoji_📋_456',
            'rule@detail#789',
            'rule%get&012',
            'rule$detail*345',
        ];

        foreach ($specialIds as $ruleId) {
            $this->request->setRuleId($ruleId);
            $this->assertSame($ruleId, $this->request->getRuleId());
        }
    }

    public function testAgentMethods(): void
    {
        // 测试AgentAware trait的方法
        // 初始时agent应该为null
        $this->assertNull($this->request->getAgent());

        // 创建mock Agent
        $mockAgent = $this->createMock(AgentInterface::class);
        $mockAgent->expects($this->any())
            ->method('getAgentId')
            ->willReturn('agent_123')
        ;
        $mockAgent->expects($this->any())
            ->method('getWelcomeText')
            ->willReturn('Welcome!')
        ;

        // 设置agent
        $this->request->setAgent($mockAgent);

        // 验证getAgent返回相同的对象
        $this->assertSame($mockAgent, $this->request->getAgent());
        $this->assertNotNull($this->request->getAgent());
        $this->assertInstanceOf(AgentInterface::class, $this->request->getAgent());

        // 验证可以获取agent的属性
        $agent = $this->request->getAgent();
        $this->assertNotNull($agent);
        $this->assertSame('agent_123', $agent->getAgentId());
        $this->assertSame('Welcome!', $agent->getWelcomeText());

        // 测试设置为null
        $this->request->setAgent(null);
        $this->assertNull($this->request->getAgent());
    }

    public function testGetRequestOptionsWithRuleId(): void
    {
        // 测试包含规则ID的请求选项
        $ruleId = 'detail_rule_001';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('rule_id', $json);
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertSame($ruleId, $json['rule_id']);
    }

    public function testGetRequestOptionsJsonStructure(): void
    {
        // 测试JSON结构的正确性
        $ruleId = 'structure_test_rule';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertCount(1, $options); // 只有json键
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertNotNull($json);
        $this->assertIsArray($json);
        $this->assertCount(1, $json); // 只有rule_id键
        $this->assertArrayHasKey('rule_id', $json);
    }

    public function testBusinessScenarioGetTextRuleDetail(): void
    {
        // 测试业务场景：获取文本敏感词规则详情
        $ruleId = 'text_rule_detail_001';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertSame($ruleId, $json['rule_id']);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule', $this->request->getRequestPath());
    }

    public function testBusinessScenarioGetSemanticRuleDetail(): void
    {
        // 测试业务场景：获取语义拦截规则详情
        $ruleId = 'semantic_rule_detail_002';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertSame($ruleId, $json['rule_id']);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('rule_id', $json);
    }

    public function testBusinessScenarioGetStrictRuleDetail(): void
    {
        // 测试业务场景：获取严格拦截规则详情
        $ruleId = 'strict_intercept_rule_003';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertSame($ruleId, $json['rule_id']);
    }

    public function testBusinessScenarioGetWarningRuleDetail(): void
    {
        // 测试业务场景：获取警告规则详情
        $ruleId = 'warning_only_rule_004';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertSame($ruleId, $json['rule_id']);
    }

    public function testBusinessScenarioGetUserSpecificRuleDetail(): void
    {
        // 测试业务场景：获取用户专用规则详情
        $ruleId = 'user_specific_rule_005';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertSame($ruleId, $json['rule_id']);
    }

    public function testBusinessScenarioGetDepartmentRuleDetail(): void
    {
        // 测试业务场景：获取部门规则详情
        $ruleId = 'department_rule_006';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertSame($ruleId, $json['rule_id']);
    }

    public function testBusinessScenarioGetArchivedRuleDetail(): void
    {
        // 测试业务场景：获取归档规则详情
        $ruleId = 'archived_rule_007';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertSame($ruleId, $json['rule_id']);
    }

    public function testRuleIdRequiredForGet(): void
    {
        // 测试获取操作需要规则ID
        $this->expectException(\Error::class); // 访问未初始化的属性会抛出Error

        $this->request->getRuleId();
    }

    public function testRuleIdImmutable(): void
    {
        // 测试规则ID的不可变性（每次设置都会覆盖）
        $firstId = 'first_detail_rule_id';
        $secondId = 'second_detail_rule_id';

        $this->request->setRuleId($firstId);
        $this->assertSame($firstId, $this->request->getRuleId());

        $this->request->setRuleId($secondId);
        $this->assertSame($secondId, $this->request->getRuleId());
        $this->assertNotSame($firstId, $this->request->getRuleId());
    }

    public function testRequestPathImmutable(): void
    {
        // 测试请求路径的不可变性
        $path1 = $this->request->getRequestPath();
        $this->request->setRuleId('some_detail_rule');
        $path2 = $this->request->getRequestPath();

        $this->assertSame($path1, $path2);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule', $path1);
    }

    public function testRequestOptionsIdempotent(): void
    {
        // 测试请求选项的幂等性
        $ruleId = 'idempotent_detail_test_rule';
        $this->request->setRuleId($ruleId);

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        $this->assertNotNull($options1);
        $this->assertNotNull($options2);
        $this->assertArrayHasKey('json', $options1);
        $this->assertArrayHasKey('json', $options2);
        $this->assertEquals($options1, $options2);
        $json1 = $options1['json'];
        $json2 = $options2['json'];
        $this->assertIsArray($json1);
        $this->assertIsArray($json2);
        $this->assertSame($json1['rule_id'], $json2['rule_id']);
    }

    public function testRuleIdBoundaryCases(): void
    {
        // 测试边界情况：极短和极长的规则ID
        $shortId = 'd';
        $longId = str_repeat('detail_rule_id_', 100) . 'end';

        $this->request->setRuleId($shortId);
        $this->assertSame($shortId, $this->request->getRuleId());

        $this->request->setRuleId($longId);
        $this->assertSame($longId, $this->request->getRuleId());
    }

    public function testMultipleRuleIdChanges(): void
    {
        // 测试多次更改规则ID
        $ids = ['detail_id1', 'detail_id2', 'detail_id3', 'detail_id4', 'detail_id5'];

        foreach ($ids as $id) {
            $this->request->setRuleId($id);
            $this->assertSame($id, $this->request->getRuleId());

            $options = $this->request->getRequestOptions();
            $this->assertNotNull($options);
            $this->assertArrayHasKey('json', $options);
            $json = $options['json'];
            $this->assertIsArray($json);
            $this->assertSame($id, $json['rule_id']);
        }
    }

    public function testRequestOptionsFormat(): void
    {
        // 测试请求选项格式的一致性
        $ruleId = 'format_detail_test_rule';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);

        // 验证格式符合企业微信API要求
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('rule_id', $json);
    }

    public function testJsonOnlyContainsRuleId(): void
    {
        // 测试JSON只包含rule_id，不包含其他字段
        $ruleId = 'only_rule_id_test';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertNotNull($json);
        $this->assertIsArray($json);

        $this->assertCount(1, $json);
        $this->assertArrayHasKey('rule_id', $json);
        $this->assertArrayNotHasKey('rule_name', $json);
        $this->assertArrayNotHasKey('word_list', $json);
        $this->assertArrayNotHasKey('semantics_list', $json);
        $this->assertArrayNotHasKey('intercept_type', $json);
    }

    public function testUnicodeRuleIds(): void
    {
        // 测试Unicode字符的规则ID
        $unicodeIds = [
            'rule_规则_123',
            'правило_456',
            'ルール_789',
            '規則_012',
            'règle_345',
        ];

        foreach ($unicodeIds as $ruleId) {
            $this->request->setRuleId($ruleId);
            $this->assertSame($ruleId, $this->request->getRuleId());

            $options = $this->request->getRequestOptions();
            $this->assertNotNull($options);
            $this->assertArrayHasKey('json', $options);
            $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertSame($ruleId, $json['rule_id']);
        }
    }
}
