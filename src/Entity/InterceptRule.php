<?php

namespace WechatWorkInterceptRuleBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;
use WechatWorkInterceptRuleBundle\Repository\InterceptRuleRepository;

/**
 * 敏感词规则
 *
 * @see https://developer.work.weixin.qq.com/document/path/96346
 */
#[ORM\Entity(repositoryClass: InterceptRuleRepository::class)]
#[ORM\Table(name: 'wechat_work_intercept_rule', options: ['comment' => '敏感词管理'])]
class InterceptRule implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[ORM\ManyToOne(targetEntity: AgentInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?AgentInterface $agent = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '规则ID'])]
    private ?string $ruleId = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '规则名称'])]
    private ?string $name = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::JSON, options: ['comment' => '敏感词列表'])]
    private array $wordList = [];

    #[Groups(['admin_curd'])]
        #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '额外的拦截语义规则'])]
    private ?array $semanticsList = [];

    #[Groups(['admin_curd'])]
    #[ORM\Column(length: 10, enumType: InterceptType::class, options: ['comment' => '拦截方式'])]
    private ?InterceptType $interceptType = null;

    #[Groups(['admin_curd'])]
        #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '可使用的userid列表'])]
    private array $applicableUserList = [];

    #[Groups(['admin_curd'])]
        #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '可使用的部门列表'])]
    private array $applicableDepartmentList = [];

    #[TrackColumn]
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '已同步', 'default' => 0])]
    private ?bool $sync = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): self
    {
        $this->corp = $corp;

        return $this;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(?AgentInterface $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWordList(): array
    {
        return $this->wordList;
    }

    public function setWordList(array $wordList): self
    {
        $this->wordList = $wordList;

        return $this;
    }

    public function getSemanticsList(): ?array
    {
        return $this->semanticsList;
    }

    public function setSemanticsList(?array $semanticsList): self
    {
        if (!empty($semanticsList)) {
            \sort($semanticsList);
        }

        $this->semanticsList = $semanticsList;

        return $this;
    }

    public function getInterceptType(): ?InterceptType
    {
        return $this->interceptType;
    }

    public function setInterceptType(InterceptType $interceptType): self
    {
        $this->interceptType = $interceptType;

        return $this;
    }

    public function getApplicableUserList(): array
    {
        return $this->applicableUserList;
    }

    public function setApplicableUserList(?array $applicableUserList): self
    {
        $this->applicableUserList = $applicableUserList;

        return $this;
    }

    public function getApplicableDepartmentList(): array
    {
        return $this->applicableDepartmentList;
    }

    public function setApplicableDepartmentList(?array $applicableDepartmentList): self
    {
        $this->applicableDepartmentList = $applicableDepartmentList;

        return $this;
    }

    public function getRuleId(): ?string
    {
        return $this->ruleId;
    }

    public function setRuleId(?string $ruleId): self
    {
        $this->ruleId = $ruleId;

        return $this;
    }

    public function isSync(): ?bool
    {
        return $this->sync;
    }

    public function setSync(?bool $sync): self
    {
        $this->sync = $sync;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}