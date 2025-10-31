<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineDirectInsertBundle\DoctrineDirectInsertBundle;
use WechatWorkBundle\WechatWorkBundle;

class WechatWorkInterceptRuleBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            WechatWorkBundle::class => ['all' => true],
            DoctrineDirectInsertBundle::class => ['all' => true],
        ];
    }
}
