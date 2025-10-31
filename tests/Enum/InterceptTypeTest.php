<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;

/**
 * InterceptType枚举测试
 *
 * @internal
 */
#[CoversClass(InterceptType::class)]
final class InterceptTypeTest extends AbstractEnumTestCase
{
    #[TestWith([InterceptType::WARN, '1', '警告并拦截发送'])]
    #[TestWith([InterceptType::NOTICE, '2', '仅发警告'])]
    public function testEnumValueAndLabel(InterceptType $enum, string $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
    }

    public function testEnumCasesHasCorrectCount(): void
    {
        // 测试枚举数量
        $cases = InterceptType::cases();
        $this->assertCount(2, $cases);
    }

    public function testEnumCasesContainsExpectedValues(): void
    {
        // 测试所有枚举值
        $cases = InterceptType::cases();
        $values = array_map(fn ($case) => $case->value, $cases);

        $this->assertContains('1', $values);
        $this->assertContains('2', $values);
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn ($case) => $case->value, InterceptType::cases());
        $this->assertSame(count($values), count(array_unique($values)));
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), InterceptType::cases());
        $this->assertSame(count($labels), count(array_unique($labels)));
    }

    public function testGetLabelReturnsNonEmptyStrings(): void
    {
        // 测试标签非空
        foreach (InterceptType::cases() as $case) {
            $label = $case->getLabel();
            $this->assertNotEmpty($label);
        }
    }

    public function testGetLabelUniqueLabels(): void
    {
        // 测试标签唯一性
        $labels = [];
        foreach (InterceptType::cases() as $case) {
            $labels[] = $case->getLabel();
        }

        $this->assertSame(count($labels), count(array_unique($labels)));
    }

    public function testFromStringWithValidValues(): void
    {
        // 测试字符串转换为枚举
        $warn = InterceptType::from('1');
        $notice = InterceptType::from('2');

        $this->assertSame(InterceptType::WARN, $warn);
        $this->assertSame(InterceptType::NOTICE, $notice);
    }

    #[TestWith(['invalid'])]
    #[TestWith(['99'])]
    #[TestWith(['0'])]
    public function testTryFromInvalidInputReturnsNull(mixed $invalidInput): void
    {
        /** @phpstan-ignore argument.type */
        $result = InterceptType::tryFrom($invalidInput);
        $this->assertNull($result);
    }

    #[TestWith(['invalid'])]
    #[TestWith(['99'])]
    #[TestWith(['0'])]
    public function testFromInvalidInputThrowsException(string $invalidInput): void
    {
        $this->expectException(\ValueError::class);
        InterceptType::from($invalidInput);
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        // 测试枚举实现的接口
        $this->assertInstanceOf(Labelable::class, InterceptType::WARN);
        $this->assertInstanceOf(Itemable::class, InterceptType::WARN);
        $this->assertInstanceOf(Selectable::class, InterceptType::WARN);
    }

    public function testEnumUsesExpectedTraits(): void
    {
        // 测试Trait方法的功能，而不是存在性
        // 如果方法不存在，测试会在调用时失败
        // 这些方法存在并返回数组
        $array = InterceptType::WARN->toArray();
        $this->assertCount(2, $array); // 应该有 value 和 label

        $options = InterceptType::genOptions();
        $this->assertCount(2, $options); // 有两个枚举值

        $item = InterceptType::WARN->toArray();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
    }

    public function testGenOptionsReturnArray(): void
    {
        // 测试genOptions方法
        $options = InterceptType::genOptions();
        $this->assertCount(2, $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
        }

        // 验证选项包含预期值
        $values = array_column($options, 'value');
        $this->assertContains('1', $values);
        $this->assertContains('2', $values);
    }

    public function testToArrayWithValidEnum(): void
    {
        // 测试toArray方法
        $array = InterceptType::WARN->toArray();
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame('1', $array['value']);
        $this->assertSame('警告并拦截发送', $array['label']);
    }

    public function testToSelectItemWithValidEnum(): void
    {
        // 测试toSelectItem方法
        $item = InterceptType::NOTICE->toSelectItem();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertSame('2', $item['value']);
        $this->assertSame('仅发警告', $item['label']);
    }

    public function testBusinessScenariosWarnType(): void
    {
        // 测试业务场景：警告并拦截
        $warnType = InterceptType::WARN;

        $this->assertSame('1', $warnType->value);
        $this->assertSame('警告并拦截发送', $warnType->getLabel());
        $this->assertStringContainsString('拦截', $warnType->getLabel());
    }

    public function testBusinessScenariosNoticeType(): void
    {
        // 测试业务场景：仅发警告
        $noticeType = InterceptType::NOTICE;

        $this->assertSame('2', $noticeType->value);
        $this->assertSame('仅发警告', $noticeType->getLabel());
        $this->assertStringContainsString('警告', $noticeType->getLabel());
        $this->assertFalse(str_contains($noticeType->getLabel(), '拦截'));
    }

    public function testEnumSerialization(): void
    {
        // 测试枚举序列化
        $warn = InterceptType::WARN;
        $serialized = serialize($warn);
        $unserialized = unserialize($serialized);

        $this->assertSame($warn, $unserialized);
        $this->assertSame($warn->value, $unserialized->value);
        $this->assertSame($warn->getLabel(), $unserialized->getLabel());
    }

    public function testEnumComparison(): void
    {
        // 测试枚举比较
        $warn1 = InterceptType::WARN;
        $warn2 = InterceptType::WARN;

        // PHP 8 枚举是单例的，相同的枚举值总是相同的实例
        $this->assertSame($warn1, $warn2);
    }

    public function testEnumInArrayCheck(): void
    {
        // 测试枚举在数组中的检查
        $types = [InterceptType::WARN, InterceptType::NOTICE];

        $this->assertContains(InterceptType::WARN, $types);
        $this->assertContains(InterceptType::NOTICE, $types);
    }

    public function testEnumSwitchStatement(): void
    {
        // 测试枚举在match表达式中的使用
        $types = InterceptType::cases();

        foreach ($types as $type) {
            $result = match ($type) {
                InterceptType::WARN => 'intercept',
                InterceptType::NOTICE => 'notice',
            };

            if (InterceptType::WARN === $type) {
                $this->assertSame('intercept', $result);
            } else {
                $this->assertSame('notice', $result);
            }
        }
    }
}
