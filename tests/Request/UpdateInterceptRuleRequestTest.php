<?php

namespace WechatWorkInterceptRuleBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkInterceptRuleBundle\Request\UpdateInterceptRuleRequest;

/**
 * UpdateInterceptRuleRequest 测试
 */
class UpdateInterceptRuleRequestTest extends TestCase
{
    private UpdateInterceptRuleRequest $request;

    protected function setUp(): void
    {
        $this->request = new UpdateInterceptRuleRequest();
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
        $this->assertContains(\WechatWorkInterceptRuleBundle\Request\BaseFieldTrait::class, $traits);
    }

    public function test_getRequestPath(): void
    {
        // 测试请求路径
        $expectedPath = '/cgi-bin/externalcontact/update_intercept_rule';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function test_ruleId_setterAndGetter(): void
    {
        // 测试规则ID设置和获取
        $ruleId = 'rule_12345';
        $this->request->setRuleId($ruleId);
        $this->assertSame($ruleId, $this->request->getRuleId());
    }

    public function test_ruleId_differentFormats(): void
    {
        // 测试不同格式的规则ID
        $ruleIds = [
            'rule_123',
            'intercept_rule_456',
            'RULE_UPPERCASE_789',
            'rule-with-dashes-012',
            'rule.with.dots.345',
            '1234567890',
            'very_long_rule_id_with_many_characters_123456789'
        ];

        foreach ($ruleIds as $ruleId) {
            $this->request->setRuleId($ruleId);
            $this->assertSame($ruleId, $this->request->getRuleId());
        }
    }

    public function test_addApplicableUserList_setterAndGetter(): void
    {
        // 测试新增用户列表设置和获取
        $userList = ['user001', 'user002', 'user003'];
        $this->request->setAddApplicableUserList($userList);
        $this->assertSame($userList, $this->request->getAddApplicableUserList());
    }

    public function test_addApplicableUserList_nullValue(): void
    {
        // 测试新增用户列表为null
        $this->request->setAddApplicableUserList(null);
        $this->assertNull($this->request->getAddApplicableUserList());
    }

    public function test_addApplicableUserList_emptyArray(): void
    {
        // 测试新增用户列表为空数组
        $emptyList = [];
        $this->request->setAddApplicableUserList($emptyList);
        $this->assertSame($emptyList, $this->request->getAddApplicableUserList());
        $this->assertCount(0, $this->request->getAddApplicableUserList());
    }

    public function test_addApplicableDepartmentList_setterAndGetter(): void
    {
        // 测试新增部门列表设置和获取
        $deptList = [100, 200, 300];
        $this->request->setAddApplicableDepartmentList($deptList);
        $this->assertSame($deptList, $this->request->getAddApplicableDepartmentList());
    }

    public function test_addApplicableDepartmentList_nullValue(): void
    {
        // 测试新增部门列表为null
        $this->request->setAddApplicableDepartmentList(null);
        $this->assertNull($this->request->getAddApplicableDepartmentList());
    }

    public function test_removeApplicableUserList_setterAndGetter(): void
    {
        // 测试移除用户列表设置和获取
        $userList = ['user001', 'user002'];
        $this->request->setRemoveApplicableUserList($userList);
        $this->assertSame($userList, $this->request->getRemoveApplicableUserList());
    }

    public function test_removeApplicableUserList_nullValue(): void
    {
        // 测试移除用户列表为null
        $this->request->setRemoveApplicableUserList(null);
        $this->assertNull($this->request->getRemoveApplicableUserList());
    }

    public function test_removeApplicableDepartmentList_setterAndGetter(): void
    {
        // 测试移除部门列表设置和获取
        $deptList = [100, 200];
        $this->request->setRemoveApplicableDepartmentList($deptList);
        $this->assertSame($deptList, $this->request->getRemoveApplicableDepartmentList());
    }

    public function test_removeApplicableDepartmentList_nullValue(): void
    {
        // 测试移除部门列表为null
        $this->request->setRemoveApplicableDepartmentList(null);
        $this->assertNull($this->request->getRemoveApplicableDepartmentList());
    }

    public function test_baseFieldTrait_functionality(): void
    {
        // 测试BaseFieldTrait功能
        $this->request->setRuleName('更新规则');
        $this->assertSame('更新规则', $this->request->getRuleName());

        $this->request->setWordList(['新敏感词1', '新敏感词2']);
        $this->assertSame(['新敏感词1', '新敏感词2'], $this->request->getWordList());

        $this->request->setSemanticsList([1, 3]);
        $this->assertSame([1, 3], $this->request->getSemanticsList());

        $this->request->setInterceptType(2);
        $this->assertSame(2, $this->request->getInterceptType());
    }

    public function test_agent_methods(): void
    {
        // 测试AgentAware trait的方法
        // 初始时agent应该为null
        $this->assertNull($this->request->getAgent());
        
        // 创建mock Agent
        $mockAgent = $this->createMock(\Tourze\WechatWorkContracts\AgentInterface::class);
        $mockAgent->expects($this->any())
            ->method('getAgentId')
            ->willReturn('agent_456');
        $mockAgent->expects($this->any())
            ->method('getWelcomeText')
            ->willReturn('Hello World!');
        
        // 设置agent
        $this->request->setAgent($mockAgent);
        
        // 验证getAgent返回相同的对象
        $this->assertSame($mockAgent, $this->request->getAgent());
        $this->assertNotNull($this->request->getAgent());
        $this->assertInstanceOf(\Tourze\WechatWorkContracts\AgentInterface::class, $this->request->getAgent());
        
        // 验证可以获取agent的属性
        $this->assertSame('agent_456', $this->request->getAgent()->getAgentId());
        $this->assertSame('Hello World!', $this->request->getAgent()->getWelcomeText());
        
        // 测试设置为null
        $this->request->setAgent(null);
        $this->assertNull($this->request->getAgent());
    }

    public function test_getRequestOptions_basicUpdate(): void
    {
        // 测试基本更新的请求选项
        $this->request->setRuleId('rule_basic_123');
        // 初始化必要的属性以避免类型错误
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        
        $this->assertArrayHasKey('rule_id', $json);
        $this->assertArrayHasKey('extra_rule', $json);
        $this->assertSame('rule_basic_123', $json['rule_id']);
        $this->assertArrayHasKey('semantics_list', $json['extra_rule']);
    }

    public function test_getRequestOptions_updateRuleName(): void
    {
        // 测试更新规则名称
        $this->request->setRuleId('rule_name_update');
        $this->request->setRuleName('新的规则名称');
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];
        
        $this->assertArrayHasKey('rule_name', $json);
        $this->assertSame('新的规则名称', $json['rule_name']);
    }

    public function test_getRequestOptions_updateWordList(): void
    {
        // 测试更新敏感词列表
        $this->request->setRuleId('rule_word_update');
        $this->request->setWordList(['新违禁词', '新敏感词']);
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];
        
        $this->assertArrayHasKey('word_list', $json);
        $this->assertSame(['新违禁词', '新敏感词'], $json['word_list']);
    }

    public function test_getRequestOptions_updateInterceptType(): void
    {
        // 测试更新拦截类型
        $this->request->setRuleId('rule_type_update');
        $this->request->setInterceptType(2);
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];
        
        $this->assertArrayHasKey('intercept_type', $json);
        $this->assertSame(2, $json['intercept_type']);
    }

    public function test_getRequestOptions_updateSemanticsList(): void
    {
        // 测试更新语义规则列表
        $this->request->setRuleId('rule_semantics_update');
        $this->request->setSemanticsList([1, 2]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];
        
        $this->assertArrayHasKey('extra_rule', $json);
        $this->assertArrayHasKey('semantics_list', $json['extra_rule']);
        $this->assertSame([1, 2], $json['extra_rule']['semantics_list']);
    }

    public function test_getRequestOptions_addUserList(): void
    {
        // 测试添加用户列表
        $this->request->setRuleId('rule_add_users');
        $this->request->setAddApplicableUserList(['new_user001', 'new_user002']);
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];
        
        $this->assertArrayHasKey('add_applicable_range', $json);
        $this->assertArrayHasKey('user_list', $json['add_applicable_range']);
        $this->assertSame(['new_user001', 'new_user002'], $json['add_applicable_range']['user_list']);
    }

    public function test_getRequestOptions_addDepartmentList(): void
    {
        // 测试添加部门列表
        $this->request->setRuleId('rule_add_depts');
        $this->request->setAddApplicableDepartmentList([400, 500]);
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];
        
        $this->assertArrayHasKey('add_applicable_range', $json);
        $this->assertArrayHasKey('department_list', $json['add_applicable_range']);
        $this->assertSame([400, 500], $json['add_applicable_range']['department_list']);
    }

    public function test_getRequestOptions_removeOperations(): void
    {
        // 测试删除操作
        $this->request->setRuleId('rule_remove');
        $this->request->setRemoveApplicableUserList(['remove_user001']);
        $this->request->setRemoveApplicableDepartmentList([600]);
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];
        
        // 根据当前源码实现，remove操作会被放在remove_applicable_range中
        $this->assertArrayHasKey('remove_applicable_range', $json);
    }

    public function test_getRequestOptions_comprehensiveUpdate(): void
    {
        // 测试全面更新
        $this->request->setRuleId('rule_comprehensive');
        $this->request->setRuleName('全面更新规则');
        $this->request->setWordList(['新词1', '新词2', '新词3']);
        $this->request->setSemanticsList([1, 2, 3]);
        $this->request->setInterceptType(1);
        $this->request->setAddApplicableUserList(['add_user001']);
        $this->request->setAddApplicableDepartmentList([700]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];
        
        $this->assertSame('rule_comprehensive', $json['rule_id']);
        $this->assertSame('全面更新规则', $json['rule_name']);
        $this->assertSame(['新词1', '新词2', '新词3'], $json['word_list']);
        $this->assertSame(1, $json['intercept_type']);
        $this->assertSame([1, 2, 3], $json['extra_rule']['semantics_list']);
        $this->assertSame(['add_user001'], $json['add_applicable_range']['user_list']);
        $this->assertSame([700], $json['add_applicable_range']['department_list']);
    }

    public function test_businessScenario_updateRuleName(): void
    {
        // 测试业务场景：更新规则名称
        $this->request->setRuleId('rule_name_change');
        $this->request->setRuleName('更新后的客服规则');
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertSame('更新后的客服规则', $json['rule_name']);
        $this->assertArrayNotHasKey('word_list', $json);
        $this->assertArrayNotHasKey('intercept_type', $json);
    }

    public function test_businessScenario_addNewUsers(): void
    {
        // 测试业务场景：为现有规则添加新用户
        $this->request->setRuleId('rule_expand_users');
        $this->request->setAddApplicableUserList([
            'new_sales001', 'new_sales002', 'new_customer_service001'
        ]);
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertArrayHasKey('add_applicable_range', $json);
        $this->assertArrayHasKey('user_list', $json['add_applicable_range']);
        $this->assertCount(3, $json['add_applicable_range']['user_list']);
        $this->assertContains('new_sales001', $json['add_applicable_range']['user_list']);
    }

    public function test_businessScenario_expandDepartments(): void
    {
        // 测试业务场景：扩展部门范围
        $this->request->setRuleId('rule_expand_depts');
        $this->request->setAddApplicableDepartmentList([800, 900, 1000]);
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertArrayHasKey('add_applicable_range', $json);
        $this->assertArrayHasKey('department_list', $json['add_applicable_range']);
        $this->assertSame([800, 900, 1000], $json['add_applicable_range']['department_list']);
    }

    public function test_businessScenario_updateSensitiveWords(): void
    {
        // 测试业务场景：更新敏感词库
        $this->request->setRuleId('rule_update_words');
        $this->request->setWordList([
            '新增违禁词1', '新增违禁词2', '更新后的敏感词',
            '竞品关键词', '投诉相关词汇'
        ]);
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertArrayHasKey('word_list', $json);
        $this->assertCount(5, $json['word_list']);
        $this->assertContains('新增违禁词1', $json['word_list']);
        $this->assertContains('竞品关键词', $json['word_list']);
    }

    public function test_businessScenario_changeInterceptStrategy(): void
    {
        // 测试业务场景：改变拦截策略
        $this->request->setRuleId('rule_change_strategy');
        $this->request->setInterceptType(2); // 从拦截改为警告
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertSame(2, $json['intercept_type']);
    }

    public function test_businessScenario_updateSemantics(): void
    {
        // 测试业务场景：更新语义拦截规则
        $this->request->setRuleId('rule_semantics_change');
        $this->request->setSemanticsList([1]); // 只拦截手机号

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertSame([1], $json['extra_rule']['semantics_list']);
    }

    public function test_ruleId_requiredForUpdate(): void
    {
        // 测试更新操作需要规则ID
        $this->expectException(\Error::class); // 访问未初始化的属性会抛出Error
        
        $this->request->getRuleId();
    }

    public function test_defaultNullValues(): void
    {
        // 测试默认的null值
        $this->assertNull($this->request->getAddApplicableUserList());
        $this->assertNull($this->request->getAddApplicableDepartmentList());
        $this->assertNull($this->request->getRemoveApplicableUserList());
        $this->assertNull($this->request->getRemoveApplicableDepartmentList());
    }

    public function test_requestPath_immutable(): void
    {
        // 测试请求路径不可变
        $path1 = $this->request->getRequestPath();
        $this->request->setRuleId('test_rule');
        $this->request->setRuleName('测试');
        $path2 = $this->request->getRequestPath();
        
        $this->assertSame($path1, $path2);
        $this->assertSame('/cgi-bin/externalcontact/update_intercept_rule', $path1);
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置操作
        $this->request->setRuleId('first_rule');
        $this->request->setRuleId('second_rule');
        $this->assertSame('second_rule', $this->request->getRuleId());

        $this->request->setAddApplicableUserList(['user1']);
        $this->request->setAddApplicableUserList(['user2', 'user3']);
        $this->assertSame(['user2', 'user3'], $this->request->getAddApplicableUserList());

        $this->request->setRemoveApplicableUserList(['remove1']);
        $this->request->setRemoveApplicableUserList(['remove2']);
        $this->assertSame(['remove2'], $this->request->getRemoveApplicableUserList());
    }

    public function test_maxSizeArrays(): void
    {
        // 测试最大大小数组（1000个节点）
        $largeUserList = [];
        for ($i = 1; $i <= 1000; $i++) {
            $largeUserList[] = "user{$i}";
        }
        
        $largeDeptList = [];
        for ($i = 1; $i <= 1000; $i++) {
            $largeDeptList[] = $i;
        }

        $this->request->setAddApplicableUserList($largeUserList);
        $this->request->setAddApplicableDepartmentList($largeDeptList);
        $this->request->setRemoveApplicableUserList($largeUserList);
        $this->request->setRemoveApplicableDepartmentList($largeDeptList);
        
        $this->assertCount(1000, $this->request->getAddApplicableUserList());
        $this->assertCount(1000, $this->request->getAddApplicableDepartmentList());
        $this->assertCount(1000, $this->request->getRemoveApplicableUserList());
        $this->assertCount(1000, $this->request->getRemoveApplicableDepartmentList());
    }

    public function test_requestOptionsStructure(): void
    {
        // 测试请求选项结构
        $this->request->setRuleId('structure_test');
        // 初始化必要的属性
        $this->request->setSemanticsList([]);

        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertCount(1, $options);
        
        $json = $options['json'];
        $this->assertArrayHasKey('rule_id', $json);
        $this->assertArrayHasKey('extra_rule', $json);
    }

    public function test_specialCharactersInRuleId(): void
    {
        // 测试规则ID中的特殊字符
        $specialRuleIds = [
            'rule@domain.com',
            'rule_with_underscore',
            'rule-with-dash',
            'rule.with.dots',
            'rule123456',
            'RuleWithUpperCase',
            'rule中文123'
        ];

        foreach ($specialRuleIds as $ruleId) {
            $this->request->setRuleId($ruleId);
            $this->assertSame($ruleId, $this->request->getRuleId());
        }
    }
} 