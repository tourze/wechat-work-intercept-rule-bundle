<?php

namespace WechatWorkInterceptRuleBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;

/**
 * InterceptType枚举测试
 */
class InterceptTypeTest extends TestCase
{
    public function test_enumCases_hasCorrectValues(): void
    {
        // 测试枚举值
        $this->assertSame('1', InterceptType::WARN->value);
        $this->assertSame('2', InterceptType::NOTICE->value);
    }

    public function test_enumCases_hasCorrectCount(): void
    {
        // 测试枚举数量
        $cases = InterceptType::cases();
        $this->assertCount(2, $cases);
    }

    public function test_enumCases_containsExpectedValues(): void
    {
        // 测试所有枚举值
        $cases = InterceptType::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        
        $this->assertContains('1', $values);
        $this->assertContains('2', $values);
    }

    public function test_getLabel_returnCorrectLabels(): void
    {
        // 测试标签方法
        $this->assertSame('警告并拦截发送', InterceptType::WARN->getLabel());
        $this->assertSame('仅发警告', InterceptType::NOTICE->getLabel());
    }

    public function test_getLabel_returnsNonEmptyStrings(): void
    {
        // 测试标签非空
        foreach (InterceptType::cases() as $case) {
            $label = $case->getLabel();
            $this->assertNotEmpty($label);
        }
    }

    public function test_getLabel_uniqueLabels(): void
    {
        // 测试标签唯一性
        $labels = [];
        foreach (InterceptType::cases() as $case) {
            $labels[] = $case->getLabel();
        }
        
        $this->assertSame(count($labels), count(array_unique($labels)));
    }

    public function test_fromString_withValidValues(): void
    {
        // 测试字符串转换为枚举
        $warn = InterceptType::from('1');
        $notice = InterceptType::from('2');
        
        $this->assertSame(InterceptType::WARN, $warn);
        $this->assertSame(InterceptType::NOTICE, $notice);
    }

    public function test_fromString_withInvalidValue_throwsException(): void
    {
        // 测试无效值抛出异常
        $this->expectException(\ValueError::class);
        InterceptType::from('invalid');
    }

    public function test_fromString_withInvalidValue_numeric_throwsException(): void
    {
        // 测试无效数字字符串抛出异常
        $this->expectException(\ValueError::class);
        InterceptType::from('99');
    }

    public function test_tryFromString_withValidValues(): void
    {
        // 测试tryFrom方法
        $warn = InterceptType::tryFrom('1');
        $notice = InterceptType::tryFrom('2');
        
        $this->assertSame(InterceptType::WARN, $warn);
        $this->assertSame(InterceptType::NOTICE, $notice);
    }

    public function test_tryFromString_withInvalidValue_returnsNull(): void
    {
        // 测试tryFrom方法返回null
        $result = InterceptType::tryFrom('invalid');
        $this->assertNull($result);
        
        $result = InterceptType::tryFrom('99');
        $this->assertNull($result);
    }

    public function test_enumImplementsExpectedInterfaces(): void
    {
        // 测试枚举实现的接口
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, InterceptType::WARN);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, InterceptType::WARN);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, InterceptType::WARN);
    }

    public function test_enumUsesExpectedTraits(): void
    {
        // 测试Trait方法的功能，而不是存在性
        // 如果方法不存在，测试会在调用时失败
        // 这些方法存在并返回数组
        $array = InterceptType::WARN->toArray();
        $this->assertCount(2, $array); // 应该有 value 和 label
        
        $options = InterceptType::genOptions();
        $this->assertCount(2, $options); // 有两个枚举值
        
        $item = InterceptType::WARN->toSelectItem();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
    }

    public function test_genOptions_returnArray(): void
    {
        // 测试genOptions方法
        $options = InterceptType::genOptions();
        $this->assertCount(2, $options);
        
        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('text', $option);
            $this->assertArrayHasKey('name', $option);
        }
        
        // 验证选项包含预期值
        $values = array_column($options, 'value');
        $this->assertContains('1', $values);
        $this->assertContains('2', $values);
    }

    public function test_toArray_withValidEnum(): void
    {
        // 测试toArray方法
        $array = InterceptType::WARN->toArray();
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame('1', $array['value']);
        $this->assertSame('警告并拦截发送', $array['label']);
    }

    public function test_toSelectItem_withValidEnum(): void
    {
        // 测试toSelectItem方法
        $item = InterceptType::NOTICE->toSelectItem();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertArrayHasKey('text', $item);
        $this->assertArrayHasKey('name', $item);
        $this->assertSame('2', $item['value']);
        $this->assertSame('仅发警告', $item['label']);
    }

    public function test_businessScenarios_warnType(): void
    {
        // 测试业务场景：警告并拦截
        $warnType = InterceptType::WARN;
        
        $this->assertSame('1', $warnType->value);
        $this->assertSame('警告并拦截发送', $warnType->getLabel());
        $this->assertTrue(str_contains($warnType->getLabel(), '拦截'));
    }

    public function test_businessScenarios_noticeType(): void
    {
        // 测试业务场景：仅发警告
        $noticeType = InterceptType::NOTICE;
        
        $this->assertSame('2', $noticeType->value);
        $this->assertSame('仅发警告', $noticeType->getLabel());
        $this->assertTrue(str_contains($noticeType->getLabel(), '警告'));
        $this->assertFalse(str_contains($noticeType->getLabel(), '拦截'));
    }

    public function test_enumSerialization(): void
    {
        // 测试枚举序列化
        $warn = InterceptType::WARN;
        $serialized = serialize($warn);
        $unserialized = unserialize($serialized);
        
        $this->assertSame($warn, $unserialized);
        $this->assertSame($warn->value, $unserialized->value);
        $this->assertSame($warn->getLabel(), $unserialized->getLabel());
    }

    public function test_enumComparison(): void
    {
        // 测试枚举比较
        $warn1 = InterceptType::WARN;
        $warn2 = InterceptType::WARN;
        $notice = InterceptType::NOTICE;
        
        // PHP 8 枚举是单例的，相同的枚举值总是相同的实例
        $this->assertSame($warn1, $warn2);
        $this->assertNotSame($warn1, $notice);
    }

    public function test_enumInArrayCheck(): void
    {
        // 测试枚举在数组中的检查
        $types = [InterceptType::WARN, InterceptType::NOTICE];
        
        $this->assertTrue(in_array(InterceptType::WARN, $types, true));
        $this->assertTrue(in_array(InterceptType::NOTICE, $types, true));
    }

    public function test_enumSwitchStatement(): void
    {
        // 测试枚举在match表达式中的使用
        $types = InterceptType::cases();
        
        foreach ($types as $type) {
            $result = match($type) {
                InterceptType::WARN => 'intercept',
                InterceptType::NOTICE => 'notice'
            };
            
            if ($type === InterceptType::WARN) {
                $this->assertSame('intercept', $result);
            } else {
                $this->assertSame('notice', $result);
            }
        }
    }
} 