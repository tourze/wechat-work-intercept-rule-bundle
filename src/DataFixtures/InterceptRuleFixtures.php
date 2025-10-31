<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkBundle\DataFixtures\AgentFixtures;
use WechatWorkBundle\DataFixtures\CorpFixtures;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;

/**
 * 敏感词拦截规则数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class InterceptRuleFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const RULE_REFERENCE_PREFIX = 'intercept-rule-';

    public static function getGroups(): array
    {
        return ['wechat-work-intercept-rule'];
    }

    public function getDependencies(): array
    {
        return [
            CorpFixtures::class,
            AgentFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $corp = $this->getReference(CorpFixtures::CORP_1_REFERENCE, Corp::class);
        $agent = $this->getReference(AgentFixtures::AGENT_1_REFERENCE, Agent::class);

        $ruleCount = 0;

        $rules = [
            [
                'name' => '政治敏感词规则',
                'wordList' => ['政治敏感', '暴力革命', '非法集会'],
                'semanticsList' => [], // 政治敏感词不使用语义规则
                'interceptType' => InterceptType::WARN,
                'applicableUserList' => ['user001', 'user002'],
                'applicableDepartmentList' => [1, 2],
            ],
            [
                'name' => '商业机密保护',
                'wordList' => ['商业机密', '内部资料', '保密信息'],
                'semanticsList' => [], // 商业机密不使用语义规则
                'interceptType' => InterceptType::NOTICE,
                'applicableUserList' => ['user003'],
                'applicableDepartmentList' => [3],
            ],
            [
                'name' => '联系方式拦截',
                'wordList' => [],
                'semanticsList' => [1, 2], // 1：手机号、2：邮箱
                'interceptType' => InterceptType::WARN,
                'applicableUserList' => [],
                'applicableDepartmentList' => [],
            ],
        ];

        foreach ($rules as $ruleData) {
            $rule = new InterceptRule();
            $rule->setCorp($corp);
            $rule->setAgent($agent);
            $rule->setName($ruleData['name']);
            $rule->setWordList($ruleData['wordList']);
            $rule->setSemanticsList($ruleData['semanticsList']);
            $rule->setInterceptType($ruleData['interceptType']);
            $rule->setApplicableUserList($ruleData['applicableUserList']);
            $rule->setApplicableDepartmentList($ruleData['applicableDepartmentList']);
            $rule->setSync(false);
            $rule->setRuleId('rule_' . (++$ruleCount));

            $manager->persist($rule);
            $this->addReference(self::RULE_REFERENCE_PREFIX . $ruleCount, $rule);
        }

        $manager->flush();
    }
}
