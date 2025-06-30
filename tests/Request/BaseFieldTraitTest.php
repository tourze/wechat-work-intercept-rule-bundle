<?php

namespace WechatWorkInterceptRuleBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkInterceptRuleBundle\Request\BaseFieldTrait;

/**
 * InterceptRule BaseFieldTrait 测试
 * 创建一个测试用的具体类来测试trait功能
 */
class BaseFieldTraitTest extends TestCase
{
    private BaseFieldTraitTestClass $instance;

    protected function setUp(): void
    {
        $this->instance = new BaseFieldTraitTestClass();
    }

    public function test_ruleName_setterAndGetter(): void
    {
        // 测试规则名称设置和获取
        $ruleName = '敏感词拦截规则';
        $this->instance->setRuleName($ruleName);
        $this->assertSame($ruleName, $this->instance->getRuleName());
    }

    public function test_ruleName_differentFormats(): void
    {
        // 测试不同格式的规则名称
        $names = [
            '简单规则',
            'Simple Rule',
            '混合中文English123',
            '特殊字符@#$%',
            '很长的规则名称测试长度限制UTF8',
            '1234567890',
            'A',
            '单'
        ];

        foreach ($names as $name) {
            $this->instance->setRuleName($name);
            $this->assertSame($name, $this->instance->getRuleName());
        }
    }

    public function test_ruleName_maxLength(): void
    {
        // 测试规则名称最大长度（20个UTF8字符）
        $maxLengthName = str_repeat('规', 20); // 20个字符
        $this->instance->setRuleName($maxLengthName);
        $this->assertSame($maxLengthName, $this->instance->getRuleName());
        $this->assertSame(20, mb_strlen($this->instance->getRuleName()));
    }

    public function test_wordList_setterAndGetter(): void
    {
        // 测试敏感词列表设置和获取
        $wordList = ['违禁词1', '违禁词2', '敏感内容'];
        $this->instance->setWordList($wordList);
        $this->assertSame($wordList, $this->instance->getWordList());
    }

    public function test_wordList_emptyArray(): void
    {
        // 测试空敏感词列表
        $emptyList = [];
        $this->instance->setWordList($emptyList);
        $this->assertSame($emptyList, $this->instance->getWordList());
        $this->assertCount(0, $this->instance->getWordList());
    }

    public function test_wordList_singleWord(): void
    {
        // 测试单个敏感词
        $singleWord = ['违禁'];
        $this->instance->setWordList($singleWord);
        $this->assertSame($singleWord, $this->instance->getWordList());
        $this->assertCount(1, $this->instance->getWordList());
    }

    public function test_wordList_multipleWords(): void
    {
        // 测试多个敏感词
        $multipleWords = [
            '违禁词1',
            '敏感内容',
            'prohibited',
            '特殊字符@#$',
            '数字123',
            '很长的敏感词测试UTF8字符长度限制'
        ];
        $this->instance->setWordList($multipleWords);
        $this->assertSame($multipleWords, $this->instance->getWordList());
        $this->assertCount(6, $this->instance->getWordList());
    }

    public function test_wordList_maxSize(): void
    {
        // 测试敏感词列表最大大小（300个）
        $largeWordList = [];
        for ($i = 1; $i <= 300; $i++) {
            $largeWordList[] = "敏感词{$i}";
        }
        
        $this->instance->setWordList($largeWordList);
        $this->assertSame($largeWordList, $this->instance->getWordList());
        $this->assertCount(300, $this->instance->getWordList());
    }

    public function test_wordList_maxWordLength(): void
    {
        // 测试敏感词最大长度（32个UTF8字符）
        $maxLengthWord = str_repeat('敏', 32); // 32个字符
        $wordList = [$maxLengthWord];
        
        $this->instance->setWordList($wordList);
        $this->assertSame($wordList, $this->instance->getWordList());
        $this->assertSame(32, mb_strlen($this->instance->getWordList()[0]));
    }

    public function test_semanticsList_setterAndGetter(): void
    {
        // 测试语义规则列表设置和获取
        $semanticsList = [1, 2, 3]; // 手机号、邮箱、红包
        $this->instance->setSemanticsList($semanticsList);
        $this->assertSame($semanticsList, $this->instance->getSemanticsList());
    }

    public function test_semanticsList_emptyArray(): void
    {
        // 测试空语义规则列表
        $emptyList = [];
        $this->instance->setSemanticsList($emptyList);
        $this->assertSame($emptyList, $this->instance->getSemanticsList());
        $this->assertCount(0, $this->instance->getSemanticsList());
    }

    public function test_semanticsList_phoneOnly(): void
    {
        // 测试只拦截手机号
        $phoneOnly = [1]; // 手机号
        $this->instance->setSemanticsList($phoneOnly);
        $this->assertSame($phoneOnly, $this->instance->getSemanticsList());
        $this->assertContains(1, $this->instance->getSemanticsList());
    }

    public function test_semanticsList_emailOnly(): void
    {
        // 测试只拦截邮箱
        $emailOnly = [2]; // 邮箱
        $this->instance->setSemanticsList($emailOnly);
        $this->assertSame($emailOnly, $this->instance->getSemanticsList());
        $this->assertContains(2, $this->instance->getSemanticsList());
    }

    public function test_semanticsList_redPacketOnly(): void
    {
        // 测试只拦截红包
        $redPacketOnly = [3]; // 红包
        $this->instance->setSemanticsList($redPacketOnly);
        $this->assertSame($redPacketOnly, $this->instance->getSemanticsList());
        $this->assertContains(3, $this->instance->getSemanticsList());
    }

    public function test_semanticsList_allTypes(): void
    {
        // 测试拦截所有类型
        $allTypes = [1, 2, 3]; // 手机号、邮箱、红包
        $this->instance->setSemanticsList($allTypes);
        $this->assertSame($allTypes, $this->instance->getSemanticsList());
        $this->assertCount(3, $this->instance->getSemanticsList());
        $this->assertContains(1, $this->instance->getSemanticsList());
        $this->assertContains(2, $this->instance->getSemanticsList());
        $this->assertContains(3, $this->instance->getSemanticsList());
    }

    public function test_interceptType_setterAndGetter(): void
    {
        // 测试拦截方式设置和获取
        $this->instance->setInterceptType(1);
        $this->assertSame(1, $this->instance->getInterceptType());

        $this->instance->setInterceptType(2);
        $this->assertSame(2, $this->instance->getInterceptType());
    }

    public function test_interceptType_warningAndBlock(): void
    {
        // 测试警告并拦截发送模式
        $warningAndBlock = 1;
        $this->instance->setInterceptType($warningAndBlock);
        $this->assertSame($warningAndBlock, $this->instance->getInterceptType());
    }

    public function test_interceptType_warningOnly(): void
    {
        // 测试仅发警告模式
        $warningOnly = 2;
        $this->instance->setInterceptType($warningOnly);
        $this->assertSame($warningOnly, $this->instance->getInterceptType());
    }

    public function test_businessScenario_strictInterceptRule(): void
    {
        // 测试业务场景：严格拦截规则
        $this->instance->setRuleName('严格拦截规则');
        $this->instance->setWordList(['违禁词', '敏感内容', '不当言论']);
        $this->instance->setSemanticsList([1, 2, 3]); // 拦截所有类型
        $this->instance->setInterceptType(1); // 警告并拦截

        $this->assertSame('严格拦截规则', $this->instance->getRuleName());
        $this->assertCount(3, $this->instance->getWordList());
        $this->assertCount(3, $this->instance->getSemanticsList());
        $this->assertSame(1, $this->instance->getInterceptType());
    }

    public function test_businessScenario_warningOnlyRule(): void
    {
        // 测试业务场景：仅警告规则
        $this->instance->setRuleName('温和提醒规则');
        $this->instance->setWordList(['提醒词汇', '注意用词']);
        $this->instance->setSemanticsList([1]); // 只拦截手机号
        $this->instance->setInterceptType(2); // 仅发警告

        $this->assertSame('温和提醒规则', $this->instance->getRuleName());
        $this->assertCount(2, $this->instance->getWordList());
        $this->assertSame([1], $this->instance->getSemanticsList());
        $this->assertSame(2, $this->instance->getInterceptType());
    }

    public function test_businessScenario_phoneNumberRule(): void
    {
        // 测试业务场景：手机号专项拦截
        $this->instance->setRuleName('手机号拦截规则');
        $this->instance->setWordList([]); // 不设置具体词汇
        $this->instance->setSemanticsList([1]); // 只拦截手机号
        $this->instance->setInterceptType(1); // 警告并拦截

        $this->assertSame('手机号拦截规则', $this->instance->getRuleName());
        $this->assertEmpty($this->instance->getWordList());
        $this->assertSame([1], $this->instance->getSemanticsList());
        $this->assertSame(1, $this->instance->getInterceptType());
    }

    public function test_businessScenario_emailAndRedPacketRule(): void
    {
        // 测试业务场景：邮箱和红包拦截
        $this->instance->setRuleName('邮箱红包拦截');
        $this->instance->setWordList(['邮箱地址', '红包转账']);
        $this->instance->setSemanticsList([2, 3]); // 邮箱和红包
        $this->instance->setInterceptType(1); // 警告并拦截

        $this->assertSame('邮箱红包拦截', $this->instance->getRuleName());
        $this->assertCount(2, $this->instance->getWordList());
        $this->assertSame([2, 3], $this->instance->getSemanticsList());
        $this->assertSame(1, $this->instance->getInterceptType());
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置操作
        $this->instance->setRuleName('第一个规则');
        $this->instance->setRuleName('第二个规则');
        $this->assertSame('第二个规则', $this->instance->getRuleName());

        $this->instance->setWordList(['词1', '词2']);
        $this->instance->setWordList(['词3', '词4', '词5']);
        $this->assertSame(['词3', '词4', '词5'], $this->instance->getWordList());
        $this->assertCount(3, $this->instance->getWordList());

        $this->instance->setSemanticsList([1]);
        $this->instance->setSemanticsList([2, 3]);
        $this->assertSame([2, 3], $this->instance->getSemanticsList());

        $this->instance->setInterceptType(1);
        $this->instance->setInterceptType(2);
        $this->assertSame(2, $this->instance->getInterceptType());
    }

    public function test_specialCharactersInWords(): void
    {
        // 测试敏感词中的特殊字符
        $specialWords = [
            '特殊符号@#$%^&*',
            '中英混合EnglishText',
            '数字123456789',
            'URL网址http://example.com',
            '空格 测试',
            'emoji😀🎉🔥',
            '标点符号！？。，；：'
        ];

        $this->instance->setWordList($specialWords);
        $this->assertSame($specialWords, $this->instance->getWordList());
        $this->assertCount(7, $this->instance->getWordList());
    }

    public function test_edgeCases_duplicateSemantics(): void
    {
        // 测试边界情况：重复的语义规则
        $duplicateSemantics = [1, 1, 2, 2, 3, 3];
        $this->instance->setSemanticsList($duplicateSemantics);
        $this->assertSame($duplicateSemantics, $this->instance->getSemanticsList());
        $this->assertCount(6, $this->instance->getSemanticsList());
    }

    public function test_edgeCases_duplicateWords(): void
    {
        // 测试边界情况：重复的敏感词
        $duplicateWords = ['重复词', '重复词', '另一个词', '另一个词'];
        $this->instance->setWordList($duplicateWords);
        $this->assertSame($duplicateWords, $this->instance->getWordList());
        $this->assertCount(4, $this->instance->getWordList());
    }

    public function test_ruleNameWithSpaces(): void
    {
        // 测试包含空格的规则名称
        $nameWithSpaces = '带 空格 的 规则 名称';
        $this->instance->setRuleName($nameWithSpaces);
        $this->assertSame($nameWithSpaces, $this->instance->getRuleName());
    }

    public function test_immutableGettersReturnSameReference(): void
    {
        // 测试数组getter返回的是相同引用
        $wordList = ['测试词1', '测试词2'];
        $semanticsList = [1, 2];
        
        $this->instance->setWordList($wordList);
        $this->instance->setSemanticsList($semanticsList);
        
        $this->assertSame($wordList, $this->instance->getWordList());
        $this->assertSame($semanticsList, $this->instance->getSemanticsList());
    }
}

/**
 * 测试用的具体类，使用BaseFieldTrait trait
 * @internal
 * @phpstan-ignore-next-line
 */
class BaseFieldTraitTestClass
{
    use BaseFieldTrait;
    
    private ?string $agent = null;
    
    public function getAgent(): ?string
    {
        return $this->agent;
    }
    
    public function setAgent(?string $agent): void
    {
        $this->agent = $agent;
    }
} 