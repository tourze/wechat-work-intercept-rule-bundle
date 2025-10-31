<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;

/**
 * 测试代理实体
 *
 * 用于敏感词规则包的集成测试，实现 AgentInterface 接口
 */
#[ORM\Entity]
#[ORM\Table(name: 'test_agent', options: ['comment' => '测试代理实体表'])]
class TestAgent implements AgentInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '代理ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $agentId;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '代理名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '代理描述'])]
    #[Assert\Length(max: 65535)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: TestCorp::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?TestCorp $corp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgentId(): ?string
    {
        return $this->agentId;
    }

    public function setAgentId(string $agentId): void
    {
        $this->agentId = $agentId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): void
    {
        $this->corp = $corp instanceof TestCorp ? $corp : null;
    }

    public function getWelcomeText(): ?string
    {
        return 'Welcome to test agent';
    }

    public function getToken(): ?string
    {
        return 'test_token_' . $this->agentId;
    }

    public function getEncodingAESKey(): ?string
    {
        return 'test_encoding_aes_key_' . $this->agentId;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
