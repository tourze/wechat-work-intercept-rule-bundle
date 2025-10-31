<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkInterceptRuleBundle\Request\AddInterceptRuleRequest;
use WechatWorkInterceptRuleBundle\Request\BaseFieldTrait;

/**
 * AddInterceptRuleRequest 测试
 *
 * @internal
 */
#[CoversClass(AddInterceptRuleRequest::class)]
final class AddInterceptRuleRequestTest extends RequestTestCase
{
    private AddInterceptRuleRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new AddInterceptRuleRequest();
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
        $this->assertContains(BaseFieldTrait::class, $traits);
    }

    public function testGetRequestPath(): void
    {
        // 测试请求路径
        $expectedPath = '/cgi-bin/externalcontact/add_intercept_rule';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function testApplicableUserListSetterAndGetter(): void
    {
        // 测试可用用户列表设置和获取
        $userList = ['user001', 'user002', 'user003'];
        $this->request->setApplicableUserList($userList);
        $this->assertSame($userList, $this->request->getApplicableUserList());
    }

    public function testApplicableUserListEmptyArray(): void
    {
        // 测试空用户列表
        $emptyList = [];
        $this->request->setApplicableUserList($emptyList);
        $this->assertSame($emptyList, $this->request->getApplicableUserList());
        $this->assertCount(0, $this->request->getApplicableUserList());
    }

    public function testApplicableUserListMaxSize(): void
    {
        // 测试用户列表最大大小（1000个）
        $largeUserList = [];
        for ($i = 1; $i <= 1000; ++$i) {
            $largeUserList[] = "user{$i}";
        }

        $this->request->setApplicableUserList($largeUserList);
        $this->assertSame($largeUserList, $this->request->getApplicableUserList());
        $this->assertCount(1000, $this->request->getApplicableUserList());
    }

    public function testApplicableDepartmentListSetterAndGetter(): void
    {
        // 测试可用部门列表设置和获取
        $deptList = [100, 200, 300];
        $this->request->setApplicableDepartmentList($deptList);
        $this->assertSame($deptList, $this->request->getApplicableDepartmentList());
    }

    public function testApplicableDepartmentListEmptyArray(): void
    {
        // 测试空部门列表
        $emptyList = [];
        $this->request->setApplicableDepartmentList($emptyList);
        $this->assertSame($emptyList, $this->request->getApplicableDepartmentList());
        $this->assertCount(0, $this->request->getApplicableDepartmentList());
    }

    public function testApplicableDepartmentListMaxSize(): void
    {
        // 测试部门列表最大大小（1000个）
        $largeDeptList = [];
        for ($i = 1; $i <= 1000; ++$i) {
            $largeDeptList[] = $i;
        }

        $this->request->setApplicableDepartmentList($largeDeptList);
        $this->assertSame($largeDeptList, $this->request->getApplicableDepartmentList());
        $this->assertCount(1000, $this->request->getApplicableDepartmentList());
    }

    public function testBaseFieldTraitFunctionality(): void
    {
        // 测试BaseFieldTrait功能
        $this->request->setRuleName('测试规则');
        $this->assertSame('测试规则', $this->request->getRuleName());

        $this->request->setWordList(['敏感词1', '敏感词2']);
        $this->assertSame(['敏感词1', '敏感词2'], $this->request->getWordList());

        $this->request->setSemanticsList([1, 2]);
        $this->assertSame([1, 2], $this->request->getSemanticsList());

        $this->request->setInterceptType(1);
        $this->assertSame(1, $this->request->getInterceptType());
    }

    public function testAgentMethods(): void
    {
        // 测试AgentAware trait的功能
        $agent = $this->createMock(AgentInterface::class);
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());
    }

    public function testGetRequestOptionsWithUserList(): void
    {
        // 测试包含用户列表的请求选项
        $this->request->setRuleName('用户规则');
        $this->request->setWordList(['违禁词']);
        $this->request->setSemanticsList([1]);
        $this->request->setInterceptType(1);
        $this->request->setApplicableUserList(['user001', 'user002']);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertIsArray($json);

        $this->assertArrayHasKey('rule_name', $json);
        $this->assertArrayHasKey('word_list', $json);
        $this->assertArrayHasKey('semantics_list', $json);
        $this->assertArrayHasKey('intercept_type', $json);
        $this->assertArrayHasKey('applicable_range', $json);
        $this->assertIsArray($json['applicable_range']);

        $this->assertSame('用户规则', $json['rule_name']);
        $this->assertSame(['违禁词'], $json['word_list']);
        $this->assertSame([1], $json['semantics_list']);
        $this->assertSame(1, $json['intercept_type']);
        $this->assertArrayHasKey('user_list', $json['applicable_range']);
        $this->assertIsArray($json['applicable_range']);
        $this->assertSame(['user001', 'user002'], $json['applicable_range']['user_list']);
    }

    public function testGetRequestOptionsWithDepartmentList(): void
    {
        // 测试包含部门列表的请求选项
        $this->request->setRuleName('部门规则');
        $this->request->setWordList(['敏感词']);
        $this->request->setSemanticsList([2]);
        $this->request->setInterceptType(2);
        $this->request->setApplicableDepartmentList([100, 200]);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);

        $this->assertSame('部门规则', $json['rule_name']);
        $this->assertArrayHasKey('department_list', $json['applicable_range']);
        $this->assertIsArray($json['applicable_range']['department_list']);
        $this->assertSame([100, 200], $json['applicable_range']['department_list']);
        $this->assertArrayNotHasKey('user_list', $json['applicable_range']);
    }

    public function testGetRequestOptionsWithBothUserAndDepartment(): void
    {
        // 测试同时包含用户和部门列表的请求选项
        $this->request->setRuleName('混合规则');
        $this->request->setWordList(['违禁词']);
        $this->request->setSemanticsList([1, 2]);
        $this->request->setInterceptType(1);
        $this->request->setApplicableUserList(['user001']);
        $this->request->setApplicableDepartmentList([100]);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);

        $this->assertIsArray($json['applicable_range']);
        $this->assertArrayHasKey('user_list', $json['applicable_range']);
        $this->assertArrayHasKey('department_list', $json['applicable_range']);
        $this->assertIsArray($json['applicable_range']['user_list']);
        $this->assertIsArray($json['applicable_range']['department_list']);
        $this->assertSame(['user001'], $json['applicable_range']['user_list']);
        $this->assertSame([100], $json['applicable_range']['department_list']);
    }

    public function testGetRequestOptionsThrowsExceptionWhenBothEmpty(): void
    {
        // 测试用户和部门都为空时抛出异常
        $this->request->setRuleName('空规则');
        $this->request->setWordList(['词']);
        $this->request->setSemanticsList([1]);
        $this->request->setInterceptType(1);
        $this->request->setApplicableUserList([]);
        $this->request->setApplicableDepartmentList([]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('userid与department不能同时为不填');

        $this->request->getRequestOptions();
    }

    public function testBusinessScenarioStrictUserRule(): void
    {
        // 测试业务场景：严格用户拦截规则
        $this->request->setRuleName('客服严格拦截');
        $this->request->setWordList(['违禁词1', '敏感内容', '不当言论']);
        $this->request->setSemanticsList([1, 2, 3]); // 拦截所有类型
        $this->request->setInterceptType(1); // 警告并拦截
        $this->request->setApplicableUserList(['customerservice001', 'customerservice002']);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);

        $this->assertSame('客服严格拦截', $json['rule_name']);
        $this->assertSame([1, 2, 3], $json['semantics_list']);
        $this->assertSame(1, $json['intercept_type']);
        $this->assertSame(['customerservice001', 'customerservice002'], $json['applicable_range']['user_list']);
    }

    public function testBusinessScenarioDepartmentWarningRule(): void
    {
        // 测试业务场景：部门警告规则
        $this->request->setRuleName('销售部提醒规则');
        $this->request->setWordList(['价格', '折扣', '优惠']);
        $this->request->setSemanticsList([2]); // 只拦截邮箱
        $this->request->setInterceptType(2); // 仅发警告
        $this->request->setApplicableDepartmentList([100, 101, 102]); // 销售相关部门

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);

        $this->assertSame('销售部提醒规则', $json['rule_name']);
        $this->assertSame([2], $json['semantics_list']);
        $this->assertSame(2, $json['intercept_type']);
        $this->assertSame([100, 101, 102], $json['applicable_range']['department_list']);
    }

    public function testBusinessScenarioPhoneNumberRule(): void
    {
        // 测试业务场景：手机号专项拦截
        $this->request->setRuleName('手机号专项拦截');
        $this->request->setWordList([]); // 不设置具体敏感词
        $this->request->setSemanticsList([1]); // 只拦截手机号
        $this->request->setInterceptType(1); // 警告并拦截
        $this->request->setApplicableUserList(['all_users']);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);

        $this->assertEmpty($json['word_list']);
        $this->assertSame([1], $json['semantics_list']);
        $this->assertSame('手机号专项拦截', $json['rule_name']);
    }

    public function testBusinessScenarioComprehensiveRule(): void
    {
        // 测试业务场景：综合拦截规则
        $this->request->setRuleName('综合安全规则');
        $this->request->setWordList([
            '违禁词1', '敏感内容', '不当言论',
            '竞品名称', '负面评价', '投诉',
        ]);
        $this->request->setSemanticsList([1, 2, 3]); // 全类型拦截
        $this->request->setInterceptType(1); // 警告并拦截
        $this->request->setApplicableUserList(['user001', 'user002']);
        $this->request->setApplicableDepartmentList([200, 300]);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);

        $this->assertIsArray($json['word_list']);
        $this->assertCount(6, $json['word_list']);
        $this->assertSame([1, 2, 3], $json['semantics_list']);
        $this->assertIsArray($json['applicable_range']);
        $this->assertArrayHasKey('user_list', $json['applicable_range']);
        $this->assertArrayHasKey('department_list', $json['applicable_range']);
    }

    public function testDefaultApplicableListsAreEmpty(): void
    {
        // 测试默认的适用列表为空
        $this->assertSame([], $this->request->getApplicableUserList());
        $this->assertSame([], $this->request->getApplicableDepartmentList());
    }

    public function testRequestPathImmutable(): void
    {
        // 测试请求路径不可变
        $path1 = $this->request->getRequestPath();
        $this->request->setRuleName('测试');
        $this->request->setApplicableUserList(['user1']);
        $path2 = $this->request->getRequestPath();

        $this->assertSame($path1, $path2);
        $this->assertSame('/cgi-bin/externalcontact/add_intercept_rule', $path1);
    }

    public function testMultipleSetOperations(): void
    {
        // 测试多次设置操作
        $this->request->setApplicableUserList(['user1']);
        $this->request->setApplicableUserList(['user2', 'user3']);
        $this->assertSame(['user2', 'user3'], $this->request->getApplicableUserList());

        $this->request->setApplicableDepartmentList([100]);
        $this->request->setApplicableDepartmentList([200, 300]);
        $this->assertSame([200, 300], $this->request->getApplicableDepartmentList());
    }

    public function testApplicableRangeEmptyByDefault(): void
    {
        // 测试适用范围的JSON结构
        $this->request->setRuleName('测试规则');
        $this->request->setWordList(['词']);
        $this->request->setSemanticsList([1]);
        $this->request->setInterceptType(1);
        $this->request->setApplicableUserList(['user1']);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertIsArray($json['applicable_range']);
        $this->assertArrayHasKey('user_list', $json['applicable_range']);
        $this->assertArrayNotHasKey('department_list', $json['applicable_range']);
    }

    public function testSpecialCharactersInUserIds(): void
    {
        // 测试用户ID中的特殊字符
        $specialUserIds = [
            'user@domain.com',
            'user_with_underscore',
            'user-with-dash',
            'user.with.dots',
            'user123456',
            'UserWithUpperCase',
        ];

        $this->request->setApplicableUserList($specialUserIds);
        $this->assertSame($specialUserIds, $this->request->getApplicableUserList());
    }

    public function testLargeUserAndDepartmentLists(): void
    {
        // 测试大型用户和部门列表
        $largeUserList = [];
        for ($i = 1; $i <= 500; ++$i) {
            $largeUserList[] = "user{$i}";
        }

        $largeDeptList = [];
        for ($i = 1; $i <= 500; ++$i) {
            $largeDeptList[] = $i;
        }

        $this->request->setApplicableUserList($largeUserList);
        $this->request->setApplicableDepartmentList($largeDeptList);

        $this->assertCount(500, $this->request->getApplicableUserList());
        $this->assertCount(500, $this->request->getApplicableDepartmentList());
    }

    public function testRequestOptionsStructure(): void
    {
        // 测试请求选项结构的完整性
        $this->request->setRuleName('结构测试');
        $this->request->setWordList(['词']);
        $this->request->setSemanticsList([1]);
        $this->request->setInterceptType(1);
        $this->request->setApplicableUserList(['user1']);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertCount(1, $options);

        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('rule_name', $json);
        $this->assertArrayHasKey('word_list', $json);
        $this->assertArrayHasKey('semantics_list', $json);
        $this->assertArrayHasKey('intercept_type', $json);
        $this->assertArrayHasKey('applicable_range', $json);
        $this->assertIsArray($json['applicable_range']);
    }
}
