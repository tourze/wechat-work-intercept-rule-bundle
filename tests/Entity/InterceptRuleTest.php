<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;
use WechatWorkInterceptRuleBundle\Tests\Entity\TestAgent;
use WechatWorkInterceptRuleBundle\Tests\Entity\TestCorp;

/**
 * InterceptRule 实体测试用例
 *
 * 测试敏感词规则实体的所有功能
 *
 * @internal
 */
#[CoversClass(InterceptRule::class)]
final class InterceptRuleTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new InterceptRule();
    }

    /**
     * @return iterable<string, array{string, array<string, string>}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'wordList' => ['wordList', ['key' => 'value']],
            'applicableUserList' => ['applicableUserList', ['key' => 'value']],
            'applicableDepartmentList' => ['applicableDepartmentList', ['key' => 'value']],
        ];
    }

    private InterceptRule $interceptRule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->interceptRule = new InterceptRule();
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $rule = new InterceptRule();

        $this->assertSame(0, $rule->getId());
        $this->assertNull($rule->getCorp());
        $this->assertNull($rule->getAgent());
        $this->assertNull($rule->getRuleId());
        $this->assertNull($rule->getName());
        $this->assertSame([], $rule->getWordList());
        $this->assertSame([], $rule->getSemanticsList());
        $this->assertNull($rule->getInterceptType());
        $this->assertSame([], $rule->getApplicableUserList());
        $this->assertSame([], $rule->getApplicableDepartmentList());
        $this->assertNull($rule->isSync());
        $this->assertNull($rule->getCreatedBy());
        $this->assertNull($rule->getUpdatedBy());
        $this->assertNull($rule->getCreateTime());
        $this->assertNull($rule->getUpdateTime());
    }

    public function testSetCorpWithValidCorpSetsCorpCorrectly(): void
    {
        $corp = new TestCorp();
        $corp->setCorpId('test_corp_123');
        $corp->setName('测试企业');

        $this->interceptRule->setCorp($corp);

        // setCorp 方法返回 void，所以不需要检查返回值
        $this->assertSame($corp, $this->interceptRule->getCorp());
    }

    public function testSetCorpWithNullSetsNull(): void
    {
        $corp = new TestCorp();
        $corp->setCorpId('test_corp_123');
        $corp->setName('测试企业');
        $this->interceptRule->setCorp($corp);

        $this->interceptRule->setCorp(null);

        // setCorp 方法返回 void，所以不需要检查返回值
        $this->assertNull($this->interceptRule->getCorp());
    }

    public function testSetAgentWithValidAgentSetsAgentCorrectly(): void
    {
        $agent = new TestAgent();
        $agent->setAgentId('1000001');
        $agent->setName('测试应用');

        $this->interceptRule->setAgent($agent);

        // setAgent 方法返回 void，所以不需要检查返回值
        $this->assertSame($agent, $this->interceptRule->getAgent());
    }

    public function testSetAgentWithNullSetsNull(): void
    {
        $agent = new TestAgent();
        $agent->setAgentId('1000001');
        $agent->setName('测试应用');
        $this->interceptRule->setAgent($agent);

        $this->interceptRule->setAgent(null);

        // setAgent 方法返回 void，所以不需要检查返回值
        $this->assertNull($this->interceptRule->getAgent());
    }

    public function testSetRuleIdWithValidIdSetsIdCorrectly(): void
    {
        $ruleId = 'rule_123456_abcdef';

        $this->interceptRule->setRuleId($ruleId);

        // setRuleId 方法返回 void，所以不需要检查返回值
        $this->assertSame($ruleId, $this->interceptRule->getRuleId());
    }

    public function testSetRuleIdWithNullSetsNull(): void
    {
        $this->interceptRule->setRuleId('old_rule_id');

        $this->interceptRule->setRuleId(null);

        // setRuleId 方法返回 void，所以不需要检查返回值
        $this->assertNull($this->interceptRule->getRuleId());
    }

    public function testSetRuleIdWithLongIdSetsLongId(): void
    {
        $longRuleId = str_repeat('a', 60); // 最大长度

        $this->interceptRule->setRuleId($longRuleId);

        // setRuleId 方法返回 void，所以不需要检查返回值
        $this->assertSame($longRuleId, $this->interceptRule->getRuleId());
    }

    public function testSetNameWithValidNameSetsNameCorrectly(): void
    {
        $name = '敏感词规则一';

        $this->interceptRule->setName($name);

        // setName 方法返回 void，所以不需要检查返回值
        $this->assertSame($name, $this->interceptRule->getName());
    }

    public function testSetNameWithLongNameSetsLongName(): void
    {
        $longName = str_repeat('规则', 10); // 20个字符，最大长度

        $this->interceptRule->setName($longName);

        // setName 方法返回 void，所以不需要检查返回值
        $this->assertSame($longName, $this->interceptRule->getName());
    }

    public function testSetWordListWithValidWordsSetsWordsCorrectly(): void
    {
        $wordList = ['敏感词1', '敏感词2', '违禁词', '不当内容'];

        $this->interceptRule->setWordList($wordList);

        // setWordList 方法返回 void，所以不需要检查返回值
        $this->assertSame($wordList, $this->interceptRule->getWordList());
    }

    public function testSetWordListWithEmptyArraySetsEmptyArray(): void
    {
        $this->interceptRule->setWordList(['old', 'words']);

        $this->interceptRule->setWordList([]);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame([], $this->interceptRule->getWordList());
    }

    public function testSetWordListWithSingleWordSetsSingleWord(): void
    {
        $wordList = ['单个敏感词'];

        $this->interceptRule->setWordList($wordList);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame($wordList, $this->interceptRule->getWordList());
    }

    public function testSetWordListWithManyWordsSetsManyWords(): void
    {
        $wordList = [];
        for ($i = 1; $i <= 100; ++$i) {
            $wordList[] = "敏感词{$i}";
        }

        $this->interceptRule->setWordList($wordList);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame($wordList, $this->interceptRule->getWordList());
        $this->assertCount(100, $this->interceptRule->getWordList());
    }

    public function testSetSemanticsListWithValidSemanticsSetsSemanticsSorted(): void
    {
        $semanticsList = [3, 1, 2];
        $expectedSorted = [1, 2, 3];

        $this->interceptRule->setSemanticsList($semanticsList);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame($expectedSorted, $this->interceptRule->getSemanticsList());
    }

    public function testSetSemanticsListWithEmptyArraySetsEmptyArray(): void
    {
        $this->interceptRule->setSemanticsList([]);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame([], $this->interceptRule->getSemanticsList());
    }

    public function testSetSemanticsListWithSingleSemanticSetsSingleSemantic(): void
    {
        $semanticsList = [42];

        $this->interceptRule->setSemanticsList($semanticsList);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame($semanticsList, $this->interceptRule->getSemanticsList());
    }

    public function testSetInterceptTypeWithWarnTypeSetsWarnType(): void
    {
        $interceptType = InterceptType::WARN;

        $this->interceptRule->setInterceptType($interceptType);

        // setInterceptType 方法返回 void，所以不需要检查返回值
        $this->assertSame($interceptType, $this->interceptRule->getInterceptType());
    }

    public function testSetInterceptTypeWithNoticeTypeSetsNoticeType(): void
    {
        $interceptType = InterceptType::NOTICE;

        $this->interceptRule->setInterceptType($interceptType);

        // setInterceptType 方法返回 void，所以不需要检查返回值
        $this->assertSame($interceptType, $this->interceptRule->getInterceptType());
    }

    public function testSetApplicableUserListWithValidUsersSetsUsersCorrectly(): void
    {
        $userList = ['user_001', 'user_002', 'user_003'];

        $this->interceptRule->setApplicableUserList($userList);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame($userList, $this->interceptRule->getApplicableUserList());
    }

    public function testSetApplicableUserListWithEmptyArraySetsEmptyArray(): void
    {
        $this->interceptRule->setApplicableUserList(['old_user']);

        $this->interceptRule->setApplicableUserList([]);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame([], $this->interceptRule->getApplicableUserList());
    }

    public function testSetApplicableDepartmentListWithValidDepartmentsSetsDepartmentsCorrectly(): void
    {
        $departmentList = [1, 2, 3, 10, 20];

        $this->interceptRule->setApplicableDepartmentList($departmentList);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame($departmentList, $this->interceptRule->getApplicableDepartmentList());
    }

    public function testSetApplicableDepartmentListWithEmptyArraySetsEmptyArray(): void
    {
        $this->interceptRule->setApplicableDepartmentList([1, 2]);

        $this->interceptRule->setApplicableDepartmentList([]);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame([], $this->interceptRule->getApplicableDepartmentList());
    }

    public function testSetSyncWithTrueSetsTrue(): void
    {
        $this->interceptRule->setSync(true);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertTrue($this->interceptRule->isSync());
    }

    public function testSetSyncWithFalseSetsFalse(): void
    {
        $this->interceptRule->setSync(false);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertFalse($this->interceptRule->isSync());
    }

    public function testSetSyncWithNullSetsNull(): void
    {
        $this->interceptRule->setSync(true);

        $this->interceptRule->setSync(null);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertNull($this->interceptRule->isSync());
    }

    public function testSetCreatedByWithValidUserSetsUserCorrectly(): void
    {
        $createdBy = 'admin_user_123';

        $this->interceptRule->setCreatedBy($createdBy);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame($createdBy, $this->interceptRule->getCreatedBy());
    }

    public function testSetCreatedByWithNullSetsNull(): void
    {
        $this->interceptRule->setCreatedBy('old_user');

        $this->interceptRule->setCreatedBy(null);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertNull($this->interceptRule->getCreatedBy());
    }

    public function testSetUpdatedByWithValidUserSetsUserCorrectly(): void
    {
        $updatedBy = 'editor_user_456';

        $this->interceptRule->setUpdatedBy($updatedBy);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertSame($updatedBy, $this->interceptRule->getUpdatedBy());
    }

    public function testSetUpdatedByWithNullSetsNull(): void
    {
        $this->interceptRule->setUpdatedBy('old_editor');

        $this->interceptRule->setUpdatedBy(null);

        // setter 方法返回 void，所以不需要检查返回值
        $this->assertNull($this->interceptRule->getUpdatedBy());
    }

    public function testSetCreateTimeWithValidDateTimeSetsTimeCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2024-01-01 08:00:00');

        $this->interceptRule->setCreateTime($createTime);

        $this->assertSame($createTime, $this->interceptRule->getCreateTime());
    }

    public function testSetCreateTimeWithNullSetsNull(): void
    {
        $this->interceptRule->setCreateTime(new \DateTimeImmutable());

        $this->interceptRule->setCreateTime(null);

        $this->assertNull($this->interceptRule->getCreateTime());
    }

    public function testSetUpdateTimeWithValidDateTimeSetsTimeCorrectly(): void
    {
        $updateTime = new \DateTimeImmutable('2024-01-30 18:30:00');

        $this->interceptRule->setUpdateTime($updateTime);

        $this->assertSame($updateTime, $this->interceptRule->getUpdateTime());
    }

    public function testSetUpdateTimeWithNullSetsNull(): void
    {
        $this->interceptRule->setUpdateTime(new \DateTimeImmutable());

        $this->interceptRule->setUpdateTime(null);

        $this->assertNull($this->interceptRule->getUpdateTime());
    }

    /**
     * 测试链式调用
     */
    public function testChainedSettersReturnSameInstance(): void
    {
        $corp = new TestCorp();
        $corp->setCorpId('test_corp_123');
        $corp->setName('测试企业');

        $agent = new TestAgent();
        $agent->setAgentId('1000001');
        $agent->setName('测试应用');

        $wordList = ['敏感词1', '敏感词2'];
        $semanticsList = [1, 2];
        $userList = ['user_001', 'user_002'];
        $departmentList = [1, 2, 3];
        $createTime = new \DateTimeImmutable('2024-01-01 08:00:00');
        $updateTime = new \DateTimeImmutable('2024-01-30 18:00:00');

        // 由于某些setter返回void，无法完全链式调用，需要分别调用
        $this->interceptRule->setCorp($corp);
        $this->interceptRule->setAgent($agent);

        // 单独调用每个setter，因为大部分返回void
        $this->interceptRule->setRuleId('chain_rule_123');
        $this->interceptRule->setName('链式测试规则');
        $this->interceptRule->setWordList($wordList);
        $this->interceptRule->setSemanticsList($semanticsList);
        $this->interceptRule->setInterceptType(InterceptType::WARN);
        $this->interceptRule->setApplicableUserList($userList);
        $this->interceptRule->setApplicableDepartmentList($departmentList);
        $this->interceptRule->setSync(true);
        $this->interceptRule->setCreatedBy('admin');
        $this->interceptRule->setUpdatedBy('editor');

        $this->interceptRule->setCreateTime($createTime);
        $this->interceptRule->setUpdateTime($updateTime);

        // 验证所有setter都正确设置了值
        $this->assertSame($corp, $this->interceptRule->getCorp());
        $this->assertSame($agent, $this->interceptRule->getAgent());
        $this->assertSame('chain_rule_123', $this->interceptRule->getRuleId());
        $this->assertSame('链式测试规则', $this->interceptRule->getName());
        $this->assertSame($wordList, $this->interceptRule->getWordList());
        $this->assertSame([1, 2], $this->interceptRule->getSemanticsList()); // 已排序
        $this->assertSame(InterceptType::WARN, $this->interceptRule->getInterceptType());
        $this->assertSame($userList, $this->interceptRule->getApplicableUserList());
        $this->assertSame($departmentList, $this->interceptRule->getApplicableDepartmentList());
        $this->assertTrue($this->interceptRule->isSync());
        $this->assertSame('admin', $this->interceptRule->getCreatedBy());
        $this->assertSame('editor', $this->interceptRule->getUpdatedBy());
        $this->assertSame($createTime, $this->interceptRule->getCreateTime());
        $this->assertSame($updateTime, $this->interceptRule->getUpdateTime());
    }

    /**
     * 测试边界场景
     */
    public function testEdgeCasesExtremeValues(): void
    {
        $maxRuleId = str_repeat('a', 60);
        $maxName = str_repeat('规', 10); // 20个字符
        $massiveWordList = [];
        for ($i = 1; $i <= 1000; ++$i) {
            $massiveWordList[] = "word_{$i}";
        }

        $this->interceptRule->setRuleId($maxRuleId);
        $this->interceptRule->setName($maxName);
        $this->interceptRule->setWordList($massiveWordList);

        $this->assertSame($maxRuleId, $this->interceptRule->getRuleId());
        $this->assertSame($maxName, $this->interceptRule->getName());
        $this->assertSame($massiveWordList, $this->interceptRule->getWordList());
        $this->assertCount(1000, $this->interceptRule->getWordList());
    }

    public function testEdgeCasesLongStrings(): void
    {
        $longWordList = [
            str_repeat('长敏感词', 100),
            str_repeat('very_long_sensitive_word', 20),
            '包含特殊字符的敏感词@#$%^&*()',
            '🚫emoji敏感词😡💢',
        ];

        $this->interceptRule->setWordList($longWordList);

        $this->assertSame($longWordList, $this->interceptRule->getWordList());
    }

    public function testEdgeCasesDateTimeTypes(): void
    {
        // 测试DateTime
        $dateTime = new \DateTimeImmutable('2024-01-15 12:30:45');
        $this->interceptRule->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->interceptRule->getCreateTime());

        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $this->interceptRule->setUpdateTime($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $this->interceptRule->getUpdateTime());
    }

    /**
     * 测试semanticsList的排序行为
     */
    public function testSemanticsListSortingSortsCorrectly(): void
    {
        $unsortedSemantics = [26, 1, 2, 3];
        $expectedSorted = [1, 2, 3, 26];

        $this->interceptRule->setSemanticsList($unsortedSemantics);

        $this->assertSame($expectedSorted, $this->interceptRule->getSemanticsList());
    }

    public function testSemanticsListSortingWithNumericStringsSortsCorrectly(): void
    {
        $numericSemantics = [10, 2, 1, 20];
        $expectedSorted = [1, 2, 10, 20]; // 数字排序

        $this->interceptRule->setSemanticsList($numericSemantics);

        $this->assertSame($expectedSorted, $this->interceptRule->getSemanticsList());
    }

    public function testSemanticsListSortingWithMixedCaseSortsCorrectly(): void
    {
        $mixedCaseSemantics = [100, 50, 25, 75];
        $expectedSorted = [25, 50, 75, 100]; // 数字排序

        $this->interceptRule->setSemanticsList($mixedCaseSemantics);

        $this->assertSame($expectedSorted, $this->interceptRule->getSemanticsList());
    }

    public function testSemanticsListSortingWithEmptyArrayDoesNotSort(): void
    {
        $this->interceptRule->setSemanticsList([]);

        $this->assertSame([], $this->interceptRule->getSemanticsList());
    }

    /**
     * 测试业务逻辑场景
     */
    public function testBusinessScenarioBasicSensitiveWordRule(): void
    {
        $corp = new TestCorp();
        $corp->setCorpId('test_corp_123');
        $corp->setName('测试企业');

        $agent = new TestAgent();
        $agent->setAgentId('1000001');
        $agent->setName('测试应用');

        $createTime = new \DateTimeImmutable('2024-01-15 10:00:00');
        $wordList = ['违禁词', '敏感内容', '不当言论'];

        // 模拟基础敏感词规则创建
        $this->interceptRule->setCorp($corp);
        $this->interceptRule->setAgent($agent);

        $this->interceptRule->setName('基础敏感词拦截');
        $this->interceptRule->setWordList($wordList);
        $this->interceptRule->setInterceptType(InterceptType::WARN);
        $this->interceptRule->setSync(false);

        $this->interceptRule->setCreateTime($createTime);
        $this->interceptRule->setCreatedBy('admin_001');

        // 验证基础规则状态
        $this->assertNotNull($this->interceptRule->getCorp());
        $this->assertNotNull($this->interceptRule->getAgent());
        $this->assertSame('基础敏感词拦截', $this->interceptRule->getName());
        $this->assertSame($wordList, $this->interceptRule->getWordList());
        $this->assertSame(InterceptType::WARN, $this->interceptRule->getInterceptType());
        $this->assertFalse($this->interceptRule->isSync());
        $this->assertSame([], $this->interceptRule->getApplicableUserList());
        $this->assertSame([], $this->interceptRule->getApplicableDepartmentList());
    }

    public function testBusinessScenarioDepartmentSpecificRule(): void
    {
        $corp = new TestCorp();
        $corp->setCorpId('test_corp_123');
        $corp->setName('测试企业');

        $agent = new TestAgent();
        $agent->setAgentId('1000001');
        $agent->setName('测试应用');

        $wordList = ['部门敏感词', '特定内容'];
        $departmentList = [10, 20, 30]; // 特定部门ID

        // 模拟部门特定规则
        $this->interceptRule->setCorp($corp);
        $this->interceptRule->setAgent($agent);

        $this->interceptRule->setName('销售部门专用规则');
        $this->interceptRule->setWordList($wordList);
        $this->interceptRule->setInterceptType(InterceptType::NOTICE);
        $this->interceptRule->setApplicableDepartmentList($departmentList);
        $this->interceptRule->setSync(true);

        $this->interceptRule->setCreatedBy('dept_admin');

        // 验证部门规则状态
        $this->assertSame(InterceptType::NOTICE, $this->interceptRule->getInterceptType());
        $this->assertSame($departmentList, $this->interceptRule->getApplicableDepartmentList());
        $this->assertSame([], $this->interceptRule->getApplicableUserList());
        $this->assertTrue($this->interceptRule->isSync());
    }

    public function testBusinessScenarioUserSpecificRule(): void
    {
        $wordList = ['用户特定敏感词'];
        $userList = ['manager_001', 'supervisor_002', 'director_003'];

        // 模拟用户特定规则
        $this->interceptRule->setName('管理层专用规则');
        $this->interceptRule->setWordList($wordList);
        $this->interceptRule->setInterceptType(InterceptType::WARN);
        $this->interceptRule->setApplicableUserList($userList);

        // 验证用户规则状态
        $this->assertSame($userList, $this->interceptRule->getApplicableUserList());
        $this->assertSame([], $this->interceptRule->getApplicableDepartmentList());
        $this->assertSame(InterceptType::WARN, $this->interceptRule->getInterceptType());
    }

    public function testBusinessScenarioSemanticsRule(): void
    {
        $semanticsList = [300, 100, 200];
        $expectedSorted = [100, 200, 300];

        // 模拟语义规则
        $this->interceptRule->setName('语义拦截规则');
        $this->interceptRule->setWordList([]); // 只使用语义，不使用具体词汇
        $this->interceptRule->setSemanticsList($semanticsList);
        $this->interceptRule->setInterceptType(InterceptType::WARN);

        // 验证语义规则状态
        $this->assertSame([], $this->interceptRule->getWordList());
        $this->assertSame($expectedSorted, $this->interceptRule->getSemanticsList());
        $this->assertSame(InterceptType::WARN, $this->interceptRule->getInterceptType());
    }

    public function testBusinessScenarioCombinedRule(): void
    {
        $wordList = ['具体敏感词1', '具体敏感词2'];
        $semanticsList = [2, 1]; // 将被排序
        $userList = ['user_001'];
        $departmentList = [5];

        // 模拟综合规则（词汇+语义+用户+部门）
        $this->interceptRule->setName('综合拦截规则');
        $this->interceptRule->setWordList($wordList);
        $this->interceptRule->setSemanticsList($semanticsList);
        $this->interceptRule->setInterceptType(InterceptType::NOTICE);
        $this->interceptRule->setApplicableUserList($userList);
        $this->interceptRule->setApplicableDepartmentList($departmentList);

        // 验证综合规则状态
        $this->assertSame($wordList, $this->interceptRule->getWordList());
        $this->assertSame([1, 2], $this->interceptRule->getSemanticsList());
        $this->assertSame($userList, $this->interceptRule->getApplicableUserList());
        $this->assertSame($departmentList, $this->interceptRule->getApplicableDepartmentList());
        $this->assertSame(InterceptType::NOTICE, $this->interceptRule->getInterceptType());
    }

    public function testBusinessScenarioRuleSyncFlow(): void
    {
        $createTime = new \DateTimeImmutable('2024-01-15 08:00:00');
        $updateTime = new \DateTimeImmutable('2024-01-15 10:30:00');

        // 模拟规则同步流程
        $this->interceptRule->setName('待同步规则');
        $this->interceptRule->setWordList(['测试敏感词']);
        $this->interceptRule->setInterceptType(InterceptType::WARN);
        $this->interceptRule->setSync(false); // 初始未同步

        $this->interceptRule->setCreateTime($createTime);
        $this->interceptRule->setCreatedBy('rule_creator');

        // 验证初始状态
        $this->assertFalse($this->interceptRule->isSync());
        $this->assertNull($this->interceptRule->getRuleId());

        // 模拟同步到企业微信后
        $this->interceptRule->setRuleId('synced_rule_remote_123');
        $this->interceptRule->setSync(true);
        $this->interceptRule->setUpdateTime($updateTime);
        $this->interceptRule->setUpdatedBy('sync_service');

        // 验证同步后状态
        $this->assertTrue($this->interceptRule->isSync());
        $this->assertNotNull($this->interceptRule->getRuleId());
        $this->assertSame('synced_rule_remote_123', $this->interceptRule->getRuleId());
        $this->assertGreaterThan($createTime, $updateTime);
    }
}
