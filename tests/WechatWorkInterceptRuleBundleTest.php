<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatWorkInterceptRuleBundle\WechatWorkInterceptRuleBundle;

/**
 * @internal
 * @phpstan-ignore symplify.forbiddenExtendOfNonAbstractClass
 */
#[CoversClass(WechatWorkInterceptRuleBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkInterceptRuleBundleTest extends AbstractBundleTestCase
{
}
