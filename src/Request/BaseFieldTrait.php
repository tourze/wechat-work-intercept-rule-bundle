<?php

namespace WechatWorkInterceptRuleBundle\Request;

trait BaseFieldTrait
{
    /**
     * @var string 规则名称，长度1~20个utf8字符
     */
    private string $ruleName;

    /**
     * @var array 敏感词列表，敏感词长度1~32个utf8字符，列表大小不能超过300个
     */
    private array $wordList;

    /**
     * @var array 额外的拦截语义规则，1：手机号、2：邮箱地:、3：红包
     */
    private array $semanticsList;

    /**
     * @var int 拦截方式，1:警告并拦截发送；2:仅发警告
     */
    private int $interceptType;

    public function getRuleName(): string
    {
        return $this->ruleName;
    }

    public function setRuleName(string $ruleName): void
    {
        $this->ruleName = $ruleName;
    }

    public function getWordList(): array
    {
        return $this->wordList;
    }

    public function setWordList(array $wordList): void
    {
        $this->wordList = $wordList;
    }

    public function getSemanticsList(): array
    {
        return $this->semanticsList;
    }

    public function setSemanticsList(array $semanticsList): void
    {
        $this->semanticsList = $semanticsList;
    }

    public function getInterceptType(): int
    {
        return $this->interceptType;
    }

    public function setInterceptType(int $interceptType): void
    {
        $this->interceptType = $interceptType;
    }
}
