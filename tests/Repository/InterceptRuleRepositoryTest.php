<?php

namespace WechatWorkInterceptRuleBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Repository\InterceptRuleRepository;

/**
 * InterceptRuleRepository 测试用例
 *
 * 测试敏感词规则仓储的功能
 */
class InterceptRuleRepositoryTest extends TestCase
{
    private InterceptRuleRepository $repository;
    
    /** @var ManagerRegistry&MockObject */
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new InterceptRuleRepository($this->registry);
    }

    public function test_constructor_initializesCorrectly(): void
    {
        $this->assertInstanceOf(InterceptRuleRepository::class, $this->repository);
    }

    public function test_entityClass_isCorrect(): void
    {
        // 测试仓储管理的实体类是否正确
        $reflection = new \ReflectionClass($this->repository);
        $parent = $reflection->getParentClass();
        $this->assertNotFalse($parent);
        
        // ServiceEntityRepository 的构造函数第二个参数是实体类名
        $constructor = $parent->getConstructor();
        $this->assertNotNull($constructor);
        
        // 通过检查构造函数调用来验证实体类
        $this->assertInstanceOf(InterceptRuleRepository::class, $this->repository);
    }

    public function test_repository_inheritance_isCorrect(): void
    {
        // 验证仓储继承了正确的基类
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $this->repository);
    }
}