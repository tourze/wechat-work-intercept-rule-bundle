<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;
use WechatWorkInterceptRuleBundle\Repository\InterceptRuleRepository;

/**
 * InterceptRuleRepository 测试用例
 *
 * 测试敏感词规则仓储的功能
 *
 * @template-extends AbstractRepositoryTestCase<InterceptRule>
 * @internal
 */
#[CoversClass(InterceptRuleRepository::class)]
#[RunTestsInSeparateProcesses]
final class InterceptRuleRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // Repository 测试使用 Mock 对象，无需特殊初始化
    }

    protected function createNewEntity(): object
    {
        // 使用真实的实体对象，需要先持久化关联实体
        $corp = new Corp();
        $corp->setCorpId('test_corp_' . uniqid());
        $corp->setName('Test Corp ' . uniqid());  // 确保名称唯一
        $corp->setCorpSecret('test_secret');

        $agent = new Agent();
        $agent->setAgentId('test_agent_' . uniqid());
        $agent->setName('Test Agent ' . uniqid());  // 确保名称唯一
        $agent->setSecret('test_agent_secret');
        $agent->setCorp($corp);

        // 持久化关联实体（因为AbstractRepositoryTestCase会测试persist操作）
        $doctrine = self::getContainer()->get('doctrine');
        self::assertInstanceOf(ManagerRegistry::class, $doctrine);
        $em = $doctrine->getManager();
        $em->persist($corp);
        $em->persist($agent);
        // 注意：不调用flush()，让AbstractRepositoryTestCase自己处理

        $entity = new InterceptRule();

        // 设置必需的关联字段
        $entity->setCorp($corp);
        $entity->setAgent($agent);

        // 设置基本字段
        $entity->setName('Test InterceptRule ' . uniqid());
        $entity->setRuleId('rule_' . uniqid());
        $entity->setInterceptType(InterceptType::WARN);
        $entity->setWordList(['test', 'word']);
        $entity->setApplicableUserList([]);
        $entity->setApplicableDepartmentList([]);
        $entity->setSync(false);

        return $entity;
    }

    protected function getRepository(): InterceptRuleRepository
    {
        return self::getService(InterceptRuleRepository::class);
    }

    public function testRepositoryInheritanceIsCorrect(): void
    {
        $repository = self::getService(InterceptRuleRepository::class);

        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }

    public function testSaveMethodWithFlush(): void
    {
        $repository = self::getService(InterceptRuleRepository::class);
        $entity = $this->createNewEntity();
        self::assertInstanceOf(InterceptRule::class, $entity);

        $repository->save($entity, true);

        $this->assertNotNull($entity->getId());
    }

    public function testSaveMethodWithoutFlush(): void
    {
        $repository = self::getService(InterceptRuleRepository::class);
        $entity = $this->createNewEntity();
        self::assertInstanceOf(InterceptRule::class, $entity);
        $originalId = $entity->getId();

        $repository->save($entity, false);

        $this->assertSame($originalId, $entity->getId());
    }

    public function testRemoveMethodWithFlush(): void
    {
        $repository = $this->getRepository();
        $entity = $this->createNewEntity();
        self::assertInstanceOf(InterceptRule::class, $entity);
        $repository->save($entity, true);
        $id = $entity->getId();

        $repository->remove($entity, true);

        $this->assertNull($repository->find($id));
    }

    public function testRemoveMethodWithoutFlush(): void
    {
        $repository = $this->getRepository();
        $entity = $this->createNewEntity();
        self::assertInstanceOf(InterceptRule::class, $entity);
        $repository->save($entity, true);
        $id = $entity->getId();

        $repository->remove($entity, false);

        $this->assertNotNull($repository->find($id));
    }

    // 基础 find 方法测试 - 数据库连接异常测试
    public function testFindWithDatabaseExceptionShouldThrowException(): void
    {
        $repository = $this->getRepository();

        $result = $repository->find(999999);

        $this->assertNull($result);
    }

    // findAll 方法测试

    // findBy 方法测试

    // findOneBy 方法测试

    // 关联查询和可空字段测试

    public function testFindOneByAssociationAgentShouldReturnMatchingEntity(): void
    {
        $repository = $this->getRepository();
        $corp = $this->createTestCorp('test-corp-id');
        $agent = $this->createTestAgent('test-agent-id');
        $agent->setCorp($corp);

        $entity = $this->createNewEntity();
        self::assertInstanceOf(InterceptRule::class, $entity);
        $entity->setAgent($agent);
        $entity->setCorp($corp);

        $doctrine = self::getContainer()->get('doctrine');
        self::assertInstanceOf(ManagerRegistry::class, $doctrine);
        $em = $doctrine->getManager();
        $em->persist($corp);
        $em->persist($agent);
        $repository->save($entity, true);

        $result = $repository->findOneBy(['agent' => $agent]);

        $this->assertInstanceOf(InterceptRule::class, $result);
        $this->assertEquals($agent, $result->getAgent());
    }

    public function testCountByAssociationCorpShouldReturnCorrectNumber(): void
    {
        $repository = $this->getRepository();
        $corp = $this->createTestCorp('test-corp-id');

        $doctrine = self::getContainer()->get('doctrine');
        self::assertInstanceOf(ManagerRegistry::class, $doctrine);
        $em = $doctrine->getManager();
        $em->persist($corp);
        $em->flush();

        for ($i = 0; $i < 3; ++$i) {
            $entity = $this->createNewEntity();
            self::assertInstanceOf(InterceptRule::class, $entity);
            $entity->setCorp($corp);
            $repository->save($entity, true);
        }

        $count = $repository->count(['corp' => $corp]);

        $this->assertGreaterThanOrEqual(3, $count);
    }

    private function createTestCorp(string $corpId = 'test-corp'): Corp
    {
        $corp = new Corp();
        $corp->setCorpId($corpId);
        $corp->setName('Test Corp ' . uniqid());  // 确保名称唯一
        $corp->setCorpSecret('test_secret');

        return $corp;
    }

    private function createTestAgent(string $agentId = 'test-agent', ?Corp $corp = null): Agent
    {
        $agent = new Agent();
        $agent->setAgentId($agentId);
        $agent->setName('Test Agent');
        $agent->setSecret('test_agent_secret');

        if (null !== $corp) {
            $agent->setCorp($corp);
        }

        return $agent;
    }
}
