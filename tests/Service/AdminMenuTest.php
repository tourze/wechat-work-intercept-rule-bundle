<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatWorkInterceptRuleBundle\Service\AdminMenu;

/**
 * AdminMenu 测试用例
 *
 * 测试敏感词规则管理后台菜单功能
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    private function createAdminMenu(): AdminMenu
    {
        return self::getService(AdminMenu::class);
    }

    public function testAdminMenuService(): void
    {
        // 测试服务可以正常实例化
        $adminMenu = $this->createAdminMenu();

        // 验证服务类名称正确
        self::assertSame(AdminMenu::class, get_class($adminMenu));
    }
}
