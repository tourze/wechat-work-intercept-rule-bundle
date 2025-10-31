<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
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
    private int $id = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[ORM\ManyToOne(targetEntity: AgentInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?AgentInterface $agent = null;

    #[Assert\Length(max: 60)]
    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '规则ID'])]
    private ?string $ruleId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '规则名称'])]
    private ?string $name = null;

    /** @var string[] */
    #[Assert\NotBlank(message: '敏感词列表不能为空')]
    #[Assert\Type(type: 'array', message: '敏感词列表必须是数组')]
    #[Assert\All(constraints: [
        new Assert\Type(type: 'string', message: '敏感词必须是字符串'),
    ])]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::JSON, options: ['comment' => '敏感词列表'])]
    private array $wordList = [];

    /** @var int[] */
    #[Assert\Type(type: 'array', message: '拦截语义规则必须是数组')]
    #[Assert\All(constraints: [
        new Assert\Type(type: 'int', message: '拦截语义必须是整数'),
    ])]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::JSON, options: ['comment' => '额外的拦截语义规则'])]
    private array $semanticsList = [];

    #[Assert\NotNull]
    #[Assert\Choice(callback: [InterceptType::class, 'cases'])]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(length: 10, enumType: InterceptType::class, options: ['comment' => '拦截方式'])]
    private ?InterceptType $interceptType = null;

    /** @var string[] */
    #[Assert\Type(type: 'array', message: '用户列表必须是数组')]
    #[Assert\All(constraints: [
        new Assert\Type(type: 'string', message: '用户ID必须是字符串'),
    ])]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::JSON, options: ['comment' => '可使用的userid列表'])]
    private array $applicableUserList = [];

    /** @var int[] */
    #[Assert\Type(type: 'array', message: '部门列表必须是数组')]
    #[Assert\All(constraints: [
        new Assert\Type(type: 'int', message: '部门ID必须是整数'),
    ])]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::JSON, options: ['comment' => '可使用的部门列表'])]
    private array $applicableDepartmentList = [];

    #[Assert\Type(type: 'bool')]
    #[TrackColumn]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '已同步', 'default' => 0])]
    private ?bool $sync = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): void
    {
        $this->corp = $corp;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(?AgentInterface $agent): void
    {
        $this->agent = $agent;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /** @return string[] */
    public function getWordList(): array
    {
        return $this->wordList;
    }

    /** @param string[] $wordList */
    public function setWordList(array $wordList): void
    {
        $this->wordList = $wordList;
    }

    /** @return int[] */
    public function getSemanticsList(): array
    {
        return $this->semanticsList;
    }

    /** @param int[] $semanticsList */
    public function setSemanticsList(array $semanticsList): void
    {
        if ([] !== $semanticsList) {
            \sort($semanticsList);
        }

        $this->semanticsList = $semanticsList;
    }

    public function getInterceptType(): ?InterceptType
    {
        return $this->interceptType;
    }

    public function setInterceptType(?InterceptType $interceptType): void
    {
        $this->interceptType = $interceptType;
    }

    /** @return string[] */
    public function getApplicableUserList(): array
    {
        return $this->applicableUserList;
    }

    /** @param string[] $applicableUserList */
    public function setApplicableUserList(array $applicableUserList): void
    {
        $this->applicableUserList = $applicableUserList;
    }

    /** @return int[] */
    public function getApplicableDepartmentList(): array
    {
        return $this->applicableDepartmentList;
    }

    /** @param int[] $applicableDepartmentList */
    public function setApplicableDepartmentList(array $applicableDepartmentList): void
    {
        $this->applicableDepartmentList = $applicableDepartmentList;
    }

    public function getRuleId(): ?string
    {
        return $this->ruleId;
    }

    public function setRuleId(?string $ruleId): void
    {
        $this->ruleId = $ruleId;
    }

    public function isSync(): ?bool
    {
        return $this->sync;
    }

    public function setSync(?bool $sync): void
    {
        $this->sync = $sync;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
