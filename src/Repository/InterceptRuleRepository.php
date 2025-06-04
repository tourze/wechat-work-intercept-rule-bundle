<?php

namespace WechatWorkInterceptRuleBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;

/**
 * @method InterceptRule|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterceptRule|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterceptRule[]    findAll()
 * @method InterceptRule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterceptRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterceptRule::class);
    }
}
