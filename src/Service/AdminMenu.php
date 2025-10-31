<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatWorkInterceptRuleBundle\Controller\Admin\InterceptRuleCrudController;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;

#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        $section = $item->addChild('wechat_work_intercept_rule', [
            'label' => '企业微信敏感词管理',
            'extras' => [
                'icon' => 'fas fa-shield-alt',
                'safe_label' => true,
            ],
        ]);

        $section->addChild('intercept_rule_crud', [
            'label' => '敏感词规则',
            'route' => $this->linkGenerator->getCurdListPage(InterceptRule::class),
            'extras' => [
                'icon' => 'fas fa-filter',
                'safe_label' => true,
            ],
        ]);
    }
}
