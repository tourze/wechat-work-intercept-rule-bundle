<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Entity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TestAgent>
 */
class TestAgentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestAgent::class);
    }

    public function findAll(): array
    {
        return [];
    }

    /**
     * @return TestAgent[]
     */
    public function findByCorpId(string $corpId): array
    {
        return [];
    }

    public function findByAgentId(string $agentId): ?TestAgent
    {
        return null;
    }
}
