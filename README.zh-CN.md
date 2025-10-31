# WechatWorkInterceptRuleBundle

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tourze/wechat-work-intercept-rule-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-intercept-rule-bundle)
[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/wechat-work-intercept-rule-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-intercept-rule-bundle)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-work-intercept-rule-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-intercept-rule-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

一个用于管理企业微信敏感词拦截规则的 Symfony Bundle。
该 Bundle 提供了同步、管理和应用企业微信应用敏感词过滤规则的功能。

## 目录

- [特性](#特性)
- [安装](#安装)
- [配置](#配置)
- [使用方法](#使用方法)
  - [实体](#实体)
    - [InterceptRule 实体](#interceptrule-实体)
    - [InterceptType 枚举](#intercepttype-枚举)
  - [命令](#命令)
    - [同步拦截规则](#同步拦截规则)
  - [API 请求](#api-请求)
  - [仓储](#仓储)
  - [事件监听器](#事件监听器)
- [高级用法](#高级用法)
  - [自定义规则处理](#自定义规则处理)
  - [批量操作](#批量操作)
  - [与外部系统集成](#与外部系统集成)
- [数据库模式](#数据库模式)
- [定时任务](#定时任务)
- [依赖](#依赖)
- [测试](#测试)
- [贡献](#贡献)
- [许可证](#许可证)
- [参考文档](#参考文档)

## 特性

- **拦截规则管理**：创建、更新和删除敏感词规则
- **自动同步**：从企业微信 API 自动同步规则
- **灵活的规则类型**：支持仅警告和拦截阻止两种规则类型
- **定时任务集成**：每 10 分钟自动同步规则
- **多应用支持**：管理多个企业微信应用的规则
- **Doctrine 集成**：完整的 ORM 支持，包含实体和仓储

## 安装

```bash
composer require tourze/wechat-work-intercept-rule-bundle
```

## 配置

在 `config/bundles.php` 中添加 Bundle：

```php
return [
    // ...
    WechatWorkInterceptRuleBundle\WechatWorkInterceptRuleBundle::class => ['all' => true],
];
```

## 使用方法

### 实体

#### InterceptRule 实体

管理敏感词规则的主要实体：

```php
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;

// 创建新的拦截规则
$rule = new InterceptRule();
$rule->setName('营销关键词');
$rule->setWordList(['垃圾邮件', '推广', '广告']);
$rule->setInterceptType(InterceptType::WARN);
```

#### InterceptType 枚举

定义检测到敏感词时的操作：

- `InterceptType::WARN`：警告并拦截发送
- `InterceptType::NOTICE`：仅发警告

### 命令

#### 同步拦截规则

从企业微信 API 同步敏感词规则：

```bash
php bin/console wechat-work:sync-intercept-rule
```

该命令：
- 从企业微信 API 获取所有拦截规则
- 创建或更新本地规则实体
- 通过定时任务每 10 分钟自动运行
- 支持多个应用和企业

### API 请求

Bundle 提供了用于企业微信 API 集成的请求类：

```php
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleListRequest;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleDetailRequest;
use WechatWorkInterceptRuleBundle\Request\AddInterceptRuleRequest;
use WechatWorkInterceptRuleBundle\Request\UpdateInterceptRuleRequest;
use WechatWorkInterceptRuleBundle\Request\DeleteInterceptRuleRequest;

// 获取拦截规则列表
$request = new GetInterceptRuleListRequest();
$request->setAgent($agent);
$response = $workService->request($request);

// 获取详细规则信息
$detailRequest = new GetInterceptRuleDetailRequest();
$detailRequest->setAgent($agent);
$detailRequest->setRuleId('rule_id');
$detail = $workService->request($detailRequest);
```

### 仓储

使用仓储进行数据库操作：

```php
use WechatWorkInterceptRuleBundle\Repository\InterceptRuleRepository;

// 注入仓储
public function __construct(
    private InterceptRuleRepository $ruleRepository
) {}

// 根据企业查找规则
$rules = $this->ruleRepository->findBy(['corp' => $corp]);

// 根据远程规则 ID 查找规则
$rule = $this->ruleRepository->findOneBy([
    'corp' => $corp,
    'ruleId' => 'remote_rule_id'
]);
```

### 事件监听器

Bundle 包含用于处理规则相关事件的事件订阅器：

```php
use WechatWorkInterceptRuleBundle\EventSubscriber\InterceptRuleListener;
```

## 高级用法

### 自定义规则处理

您可以创建自定义处理器来处理特定的规则类型：

```php
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;

class CustomRuleProcessor
{
    public function processRule(InterceptRule $rule): bool
    {
        // 自定义处理逻辑
        $wordList = $rule->getWordList();
        $interceptType = $rule->getInterceptType();
        
        // 实现您的自定义规则处理
        return $this->applyCustomLogic($wordList, $interceptType);
    }
}
```

### 批量操作

用于管理大量规则：

```php
use WechatWorkInterceptRuleBundle\Repository\InterceptRuleRepository;
use Doctrine\ORM\EntityManagerInterface;

class BulkRuleManager
{
    public function __construct(
        private InterceptRuleRepository $repository,
        private EntityManagerInterface $entityManager
    ) {}
    
    public function bulkUpdate(array $rules): void
    {
        foreach ($rules as $ruleData) {
            $rule = $this->repository->findOneBy(['ruleId' => $ruleData['id']]);
            if ($rule) {
                $rule->setWordList($ruleData['wordList']);
                $rule->setInterceptType($ruleData['interceptType']);
            }
        }
        
        $this->entityManager->flush();
    }
}
```

### 与外部系统集成

与外部内容过滤系统集成的示例：

```php
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;

class ExternalFilterIntegration
{
    public function syncWithExternalSystem(InterceptRule $rule): void
    {
        $externalData = [
            'rule_id' => $rule->getRuleId(),
            'keywords' => $rule->getWordList(),
            'action' => $rule->getInterceptType()->value,
        ];
        
        // 发送到外部系统
        $this->externalClient->updateRule($externalData);
    }
}
```

## 数据库模式

Bundle 创建以下表：

```sql
CREATE TABLE wechat_work_intercept_rule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    corp_id INT NOT NULL,
    agent_id INT NOT NULL,
    rule_id VARCHAR(60),
    name VARCHAR(20) NOT NULL,
    word_list JSON NOT NULL,
    intercept_type VARCHAR(1) NOT NULL,
    applicable_user_list JSON NOT NULL,
    applicable_department_list JSON NOT NULL,
    is_sync BOOLEAN NOT NULL DEFAULT FALSE,
    create_time DATETIME NOT NULL,
    update_time DATETIME NOT NULL,
    created_by INT,
    updated_by INT
);
```

## 定时任务

Bundle 自动注册定时任务，每 10 分钟同步规则：

```php
#[AsCronTask(expression: '*/10 * * * *')]
```

## 依赖

此 Bundle 需要：

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- WeChat Work Bundle
- 各种 Tourze 工具包

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/wechat-work-intercept-rule-bundle/tests
```

## 贡献

欢迎贡献！请提交 pull request 或为 bug 和功能请求创建 issue。

## 许可证

MIT 许可证。详情请参阅 [License File](LICENSE)。

## 参考文档

- [企业微信 API 文档](https://developer.work.weixin.qq.com/document/path/96346)
- [Symfony Bundle 文档](https://symfony.com/doc/current/bundles.html)
