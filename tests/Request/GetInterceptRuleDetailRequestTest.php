<?php

namespace WechatWorkInterceptRuleBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleDetailRequest;

/**
 * GetInterceptRuleDetailRequest 测试
 */
class GetInterceptRuleDetailRequestTest extends TestCase
{
    private GetInterceptRuleDetailRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetInterceptRuleDetailRequest();
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
        $expectedPath = '/cgi-bin/externalcontact/get_intercept_rule';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function test_ruleId_setterAndGetter(): void
    {
        // 测试规则ID设置和获取
        $ruleId = 'rule_detail_123456';
        $this->request->setRuleId($ruleId);
        $this->assertSame($ruleId, $this->request->getRuleId());
    }

    public function test_ruleId_differentFormats(): void
    {
        // 测试不同格式的规则ID
        $ruleIds = [
            'rule_detail_123',
            'get_rule_456',
            'RULE_DETAIL_UPPERCASE_789',
            'rule-detail-with-dashes-012',
            'rule.detail.with.dots.345',
            '1234567890',
            'very_long_rule_detail_id_with_many_characters_123456789'
        ];

        foreach ($ruleIds as $ruleId) {
            $this->request->setRuleId($ruleId);
            $this->assertSame($ruleId, $this->request->getRuleId());
        }
    }

    public function test_ruleId_specialCharacters(): void
    {
        // 测试包含特殊字符的规则ID
        $specialIds = [
            'rule_详情_123',
            'rule_emoji_📋_456',
            'rule@detail#789',
            'rule%get&012',
            'rule$detail*345'
        ];

        foreach ($specialIds as $ruleId) {
            $this->request->setRuleId($ruleId);
            $this->assertSame($ruleId, $this->request->getRuleId());
        }
    }

    public function test_agent_methods(): void
    {
        // 测试AgentAware trait的方法存在性
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
        $this->assertTrue(is_callable([$this->request, 'setAgent']));
        $this->assertTrue(is_callable([$this->request, 'getAgent']));
    }

    public function test_getRequestOptions_withRuleId(): void
    {
        // 测试包含规则ID的请求选项
        $ruleId = 'detail_rule_001';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('rule_id', $options['json']);
        $this->assertSame($ruleId, $options['json']['rule_id']);
    }

    public function test_getRequestOptions_jsonStructure(): void
    {
        // 测试JSON结构的正确性
        $ruleId = 'structure_test_rule';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertCount(1, $options); // 只有json键
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertCount(1, $options['json']); // 只有rule_id键
        $this->assertArrayHasKey('rule_id', $options['json']);
    }

    public function test_businessScenario_getTextRuleDetail(): void
    {
        // 测试业务场景：获取文本敏感词规则详情
        $ruleId = 'text_rule_detail_001';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($ruleId, $options['json']['rule_id']);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule', $this->request->getRequestPath());
    }

    public function test_businessScenario_getSemanticRuleDetail(): void
    {
        // 测试业务场景：获取语义拦截规则详情
        $ruleId = 'semantic_rule_detail_002';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($ruleId, $options['json']['rule_id']);
        $this->assertArrayHasKey('rule_id', $options['json']);
    }

    public function test_businessScenario_getStrictRuleDetail(): void
    {
        // 测试业务场景：获取严格拦截规则详情
        $ruleId = 'strict_intercept_rule_003';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($ruleId, $options['json']['rule_id']);
        $this->assertIsArray($options['json']);
    }

    public function test_businessScenario_getWarningRuleDetail(): void
    {
        // 测试业务场景：获取警告规则详情
        $ruleId = 'warning_only_rule_004';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($ruleId, $options['json']['rule_id']);
    }

    public function test_businessScenario_getUserSpecificRuleDetail(): void
    {
        // 测试业务场景：获取用户专用规则详情
        $ruleId = 'user_specific_rule_005';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($ruleId, $options['json']['rule_id']);
    }

    public function test_businessScenario_getDepartmentRuleDetail(): void
    {
        // 测试业务场景：获取部门规则详情
        $ruleId = 'department_rule_006';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($ruleId, $options['json']['rule_id']);
    }

    public function test_businessScenario_getArchivedRuleDetail(): void
    {
        // 测试业务场景：获取归档规则详情
        $ruleId = 'archived_rule_007';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($ruleId, $options['json']['rule_id']);
    }

    public function test_ruleId_requiredForGet(): void
    {
        // 测试获取操作需要规则ID
        $this->expectException(\Error::class); // 访问未初始化的属性会抛出Error
        
        $this->request->getRuleId();
    }

    public function test_ruleId_immutable(): void
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

    public function test_requestPath_immutable(): void
    {
        // 测试请求路径的不可变性
        $path1 = $this->request->getRequestPath();
        $this->request->setRuleId('some_detail_rule');
        $path2 = $this->request->getRequestPath();

        $this->assertSame($path1, $path2);
        $this->assertSame('/cgi-bin/externalcontact/get_intercept_rule', $path1);
    }

    public function test_requestOptions_idempotent(): void
    {
        // 测试请求选项的幂等性
        $ruleId = 'idempotent_detail_test_rule';
        $this->request->setRuleId($ruleId);

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        $this->assertEquals($options1, $options2);
        $this->assertSame($options1['json']['rule_id'], $options2['json']['rule_id']);
    }

    public function test_ruleId_boundaryCases(): void
    {
        // 测试边界情况：极短和极长的规则ID
        $shortId = 'd';
        $longId = str_repeat('detail_rule_id_', 100) . 'end';

        $this->request->setRuleId($shortId);
        $this->assertSame($shortId, $this->request->getRuleId());

        $this->request->setRuleId($longId);
        $this->assertSame($longId, $this->request->getRuleId());
    }

    public function test_multipleRuleIdChanges(): void
    {
        // 测试多次更改规则ID
        $ids = ['detail_id1', 'detail_id2', 'detail_id3', 'detail_id4', 'detail_id5'];

        foreach ($ids as $id) {
            $this->request->setRuleId($id);
            $this->assertSame($id, $this->request->getRuleId());
            
            $options = $this->request->getRequestOptions();
            $this->assertSame($id, $options['json']['rule_id']);
        }
    }

    public function test_requestOptionsFormat(): void
    {
        // 测试请求选项格式的一致性
        $ruleId = 'format_detail_test_rule';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();

        // 验证格式符合企业微信API要求
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('rule_id', $options['json']);
        $this->assertIsString($options['json']['rule_id']);
    }

    public function test_jsonOnlyContainsRuleId(): void
    {
        // 测试JSON只包含rule_id，不包含其他字段
        $ruleId = 'only_rule_id_test';
        $this->request->setRuleId($ruleId);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertCount(1, $json);
        $this->assertArrayHasKey('rule_id', $json);
        $this->assertArrayNotHasKey('rule_name', $json);
        $this->assertArrayNotHasKey('word_list', $json);
        $this->assertArrayNotHasKey('semantics_list', $json);
        $this->assertArrayNotHasKey('intercept_type', $json);
    }

    public function test_unicodeRuleIds(): void
    {
        // 测试Unicode字符的规则ID
        $unicodeIds = [
            'rule_规则_123',
            'правило_456',
            'ルール_789',
            '規則_012',
            'règle_345'
        ];

        foreach ($unicodeIds as $ruleId) {
            $this->request->setRuleId($ruleId);
            $this->assertSame($ruleId, $this->request->getRuleId());
            
            $options = $this->request->getRequestOptions();
            $this->assertSame($ruleId, $options['json']['rule_id']);
        }
    }
} 