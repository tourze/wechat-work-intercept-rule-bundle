<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Stub;

use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

class StubAgentRepository
{
    /**
     * @return Agent[]
     */
    public function findAll(): array
    {
        $corp = new Corp();
        $corp->setCorpId('test_corp_123');
        $corp->setName('测试企业');
        $corp->setCorpSecret('test_secret');

        $agent = new Agent();
        $agent->setAgentId('1000001');
        $agent->setName('测试应用');
        $agent->setCorp($corp);

        return [$agent];
    }
}
