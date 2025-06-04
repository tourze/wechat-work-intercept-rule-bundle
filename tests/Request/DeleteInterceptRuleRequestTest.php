<?php

namespace WechatWorkInterceptRuleBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkInterceptRuleBundle\Request\DeleteInterceptRuleRequest;

/**
 * DeleteInterceptRuleRequest 测试
 */
class DeleteInterceptRuleRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new DeleteInterceptRuleRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }

    public function test_ruleId_setterAndGetter(): void
    {
        // 测试规则ID设置和获取
        $request = new DeleteInterceptRuleRequest();
        $ruleId = 'rule_id_12345';
        
        $request->setRuleId($ruleId);
        $this->assertSame($ruleId, $request->getRuleId());
    }

    public function test_requestPath(): void
    {
        // 测试请求路径
        $request = new DeleteInterceptRuleRequest();
        $this->assertSame('/cgi-bin/externalcontact/del_intercept_rule', $request->getRequestPath());
    }

    public function test_requestOptions(): void
    {
        // 测试获取请求选项
        $request = new DeleteInterceptRuleRequest();
        $ruleId = 'delete_rule_001';
        
        $request->setRuleId($ruleId);
        
        $expected = [
            'json' => [
                'rule_id' => $ruleId,
            ],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptionsStructure(): void
    {
        // 测试请求选项结构
        $request = new DeleteInterceptRuleRequest();
        $request->setRuleId('test_rule_id');
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('rule_id', $options['json']);
        $this->assertCount(1, $options['json']);
    }

    public function test_businessScenario_deleteSensitiveWordRule(): void
    {
        // 测试业务场景：删除敏感词规则
        $request = new DeleteInterceptRuleRequest();
        $sensitiveRuleId = 'sensitive_word_rule_001';
        
        $request->setRuleId($sensitiveRuleId);
        
        $this->assertSame($sensitiveRuleId, $request->getRuleId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($sensitiveRuleId, $options['json']['rule_id']);
        
        // 验证API路径正确
        $this->assertSame('/cgi-bin/externalcontact/del_intercept_rule', $request->getRequestPath());
    }

    public function test_businessScenario_removeContentFilter(): void
    {
        // 测试业务场景：移除内容过滤规则
        $request = new DeleteInterceptRuleRequest();
        $filterRuleId = 'content_filter_rule_002';
        
        $request->setRuleId($filterRuleId);
        
        $options = $request->getRequestOptions();
        $this->assertSame($filterRuleId, $options['json']['rule_id']);
    }

    public function test_businessScenario_cleanupInterceptRule(): void
    {
        // 测试业务场景：清理拦截规则
        $request = new DeleteInterceptRuleRequest();
        $cleanupRuleId = 'intercept_cleanup_rule_003';
        
        $request->setRuleId($cleanupRuleId);
        
        $this->assertSame($cleanupRuleId, $request->getRuleId());
        
        // 验证API路径符合拦截规则删除要求
        $this->assertStringContainsString('intercept_rule', $request->getRequestPath());
        $this->assertStringContainsString('del', $request->getRequestPath());
    }

    public function test_ruleIdSpecialCharacters(): void
    {
        // 测试规则ID特殊字符
        $request = new DeleteInterceptRuleRequest();
        $specialRuleId = 'rule-id_with.special@chars_123';
        
        $request->setRuleId($specialRuleId);
        
        $this->assertSame($specialRuleId, $request->getRuleId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($specialRuleId, $options['json']['rule_id']);
    }

    public function test_longRuleId(): void
    {
        // 测试长规则ID
        $request = new DeleteInterceptRuleRequest();
        $longRuleId = str_repeat('rule_id_part_', 10) . 'end';
        
        $request->setRuleId($longRuleId);
        
        $this->assertSame($longRuleId, $request->getRuleId());
    }

    public function test_unicodeCharacters(): void
    {
        // 测试Unicode字符
        $request = new DeleteInterceptRuleRequest();
        $unicodeRuleId = '规则_ID_测试_123';
        
        $request->setRuleId($unicodeRuleId);
        
        $this->assertSame($unicodeRuleId, $request->getRuleId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($unicodeRuleId, $options['json']['rule_id']);
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置值
        $request = new DeleteInterceptRuleRequest();
        
        $firstRuleId = 'first_rule_id';
        $secondRuleId = 'second_rule_id';
        
        $request->setRuleId($firstRuleId);
        $this->assertSame($firstRuleId, $request->getRuleId());
        
        $request->setRuleId($secondRuleId);
        $this->assertSame($secondRuleId, $request->getRuleId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($secondRuleId, $options['json']['rule_id']);
    }

    public function test_idempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $request = new DeleteInterceptRuleRequest();
        $ruleId = 'idempotent_rule_id';
        
        $request->setRuleId($ruleId);
        
        // 多次调用应该返回相同结果
        $this->assertSame($ruleId, $request->getRuleId());
        $this->assertSame($ruleId, $request->getRuleId());
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        $this->assertSame($options1, $options2);
        
        $path1 = $request->getRequestPath();
        $path2 = $request->getRequestPath();
        $this->assertSame($path1, $path2);
    }

    public function test_immutableRequestOptions(): void
    {
        // 测试获取请求选项不会修改原始数据
        $request = new DeleteInterceptRuleRequest();
        $originalRuleId = 'original_rule_id';
        
        $request->setRuleId($originalRuleId);
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        
        // 修改返回的数组不应影响原始数据
        $options1['json']['rule_id'] = 'modified_rule_id';
        $options1['json']['new_field'] = 'new_value';
        $options1['new_key'] = 'new_value';
        
        $this->assertSame($originalRuleId, $request->getRuleId());
        $this->assertSame($originalRuleId, $options2['json']['rule_id']);
        $this->assertArrayNotHasKey('new_field', $options2['json']);
        $this->assertArrayNotHasKey('new_key', $options2);
    }

    public function test_agentAwareTrait(): void
    {
        // 测试AgentAware特性
        $request = new DeleteInterceptRuleRequest();
        
        // 测试trait提供的方法存在
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
        $this->assertTrue(is_callable([$request, 'getAgent']));
        $this->assertTrue(is_callable([$request, 'setAgent']));
    }

    public function test_emptyStringValue(): void
    {
        // 测试空字符串值
        $request = new DeleteInterceptRuleRequest();
        $request->setRuleId('');
        
        $this->assertSame('', $request->getRuleId());
        
        $options = $request->getRequestOptions();
        $this->assertSame('', $options['json']['rule_id']);
    }

    public function test_requestParametersCorrectness(): void
    {
        // 测试请求参数正确性
        $request = new DeleteInterceptRuleRequest();
        $ruleId = 'param_test_rule_id';
        
        $request->setRuleId($ruleId);
        
        $options = $request->getRequestOptions();
        
        // 验证参数结构正确
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('rule_id', $options['json']);
        $this->assertSame($ruleId, $options['json']['rule_id']);
        
        // 验证只包含必要的参数
        $this->assertCount(1, $options);
        $this->assertCount(1, $options['json']);
    }

    public function test_apiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $request = new DeleteInterceptRuleRequest();
        $path = $request->getRequestPath();
        
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('del_intercept_rule', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringEndsWith('/del_intercept_rule', $path);
    }

    public function test_jsonRequestFormat(): void
    {
        // 测试JSON请求格式
        $request = new DeleteInterceptRuleRequest();
        $ruleId = 'json_format_rule_id';
        
        $request->setRuleId($ruleId);
        
        $options = $request->getRequestOptions();
        
        // 验证使用json而不是query格式
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayNotHasKey('query', $options);
        $this->assertArrayNotHasKey('body', $options);
        $this->assertArrayNotHasKey('form_params', $options);
    }

    public function test_businessScenario_securityRuleManagement(): void
    {
        // 测试业务场景：安全规则管理
        $request = new DeleteInterceptRuleRequest();
        $securityRuleId = 'security_rule_mgmt_001';
        
        $request->setRuleId($securityRuleId);
        
        $this->assertSame($securityRuleId, $request->getRuleId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($securityRuleId, $options['json']['rule_id']);
        
        // 验证API支持安全规则删除
        $this->assertStringContainsString('del_intercept_rule', $request->getRequestPath());
    }

    public function test_businessScenario_complianceRuleRemoval(): void
    {
        // 测试业务场景：合规规则移除
        $request = new DeleteInterceptRuleRequest();
        $complianceRuleId = 'compliance_rule_002';
        
        $request->setRuleId($complianceRuleId);
        
        $options = $request->getRequestOptions();
        $this->assertSame($complianceRuleId, $options['json']['rule_id']);
    }

    public function test_businessScenario_contentModerationCleanup(): void
    {
        // 测试业务场景：内容审核清理
        $request = new DeleteInterceptRuleRequest();
        $moderationRuleId = 'content_moderation_rule_003';
        
        $request->setRuleId($moderationRuleId);
        
        $this->assertSame($moderationRuleId, $request->getRuleId());
        
        // 验证支持内容审核规则删除的参数格式
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('rule_id', $options['json']);
    }

    public function test_requestDataIntegrity(): void
    {
        // 测试请求数据完整性
        $request = new DeleteInterceptRuleRequest();
        $ruleId = 'integrity_test_rule_id';
        
        $request->setRuleId($ruleId);
        
        $options = $request->getRequestOptions();
        
        // 验证请求数据结构完整性
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($ruleId, $options['json']['rule_id']);
        
        // 验证只包含必要的字段
        $this->assertCount(1, $options);
        $this->assertCount(1, $options['json']);
    }

    public function test_ruleIdValidation(): void
    {
        // 测试规则ID验证
        $request = new DeleteInterceptRuleRequest();
        
        // 测试规则ID是必需的字符串
        $ruleId = 'validation_test_rule_id';
        $request->setRuleId($ruleId);
        $this->assertIsString($request->getRuleId());
        $this->assertSame($ruleId, $request->getRuleId());
    }

    public function test_businessScenario_batchRuleCleanup(): void
    {
        // 测试业务场景：批量规则清理（单个请求）
        $request = new DeleteInterceptRuleRequest();
        $batchRuleId = 'batch_cleanup_rule_001';
        
        $request->setRuleId($batchRuleId);
        
        $this->assertSame($batchRuleId, $request->getRuleId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($batchRuleId, $options['json']['rule_id']);
        
        // 验证API支持单个规则删除
        $this->assertStringContainsString('del_intercept_rule', $request->getRequestPath());
    }

    public function test_ruleIdFormats(): void
    {
        // 测试规则ID格式
        $request = new DeleteInterceptRuleRequest();
        $formats = [
            'simple_rule_id',
            'rule-with-dashes',
            'rule_with_underscores',
            'rule.with.dots',
            'rule123456',
            'UPPERCASE_RULE_ID',
        ];
        
        foreach ($formats as $format) {
            $request->setRuleId($format);
            $this->assertSame($format, $request->getRuleId());
            
            $options = $request->getRequestOptions();
            $this->assertSame($format, $options['json']['rule_id']);
        }
    }

    public function test_requestMethodCorrectness(): void
    {
        // 测试请求方法正确性（如果有的话）
        $request = new DeleteInterceptRuleRequest();
        
        // 验证这是一个ApiRequest实例
        $this->assertInstanceOf(ApiRequest::class, $request);
        
        // 验证请求路径和选项方法存在
        $this->assertTrue(method_exists($request, 'getRequestPath'));
        $this->assertTrue(method_exists($request, 'getRequestOptions'));
    }

    public function test_ruleIdPersistence(): void
    {
        // 测试规则ID持久性
        $request = new DeleteInterceptRuleRequest();
        $ruleId = 'persistence_test_rule_id';
        
        $request->setRuleId($ruleId);
        
        // 多次获取应保持一致
        $this->assertSame($ruleId, $request->getRuleId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($ruleId, $options['json']['rule_id']);
        
        // 再次获取选项应保持一致
        $optionsAgain = $request->getRequestOptions();
        $this->assertSame($ruleId, $optionsAgain['json']['rule_id']);
    }
} 