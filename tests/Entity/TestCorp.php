<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\WechatWorkContracts\CorpInterface;

/**
 * 测试企业实体
 *
 * 用于敏感词规则包的集成测试，实现 CorpInterface 接口
 */
#[ORM\Entity]
#[ORM\Table(name: 'test_corp', options: ['comment' => '测试企业实体表'])]
class TestCorp implements CorpInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true, options: ['comment' => '企业ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $corpId;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '企业名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '企业描述'])]
    #[Assert\Length(max: 65535)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '企业密钥'])]
    #[Assert\Length(max: 128)]
    private ?string $corpSecret = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCorpId(): string
    {
        return $this->corpId;
    }

    public function setCorpId(string $corpId): void
    {
        $this->corpId = $corpId;
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

    public function getCorpSecret(): ?string
    {
        return $this->corpSecret;
    }

    public function setCorpSecret(?string $corpSecret): void
    {
        $this->corpSecret = $corpSecret;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
