# WechatWorkInterceptRuleBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tourze/wechat-work-intercept-rule-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-intercept-rule-bundle)
[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/wechat-work-intercept-rule-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-intercept-rule-bundle)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-work-intercept-rule-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-intercept-rule-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)


A Symfony bundle for managing WeChat Work sensitive word intercept rules. 
This bundle provides functionality to synchronize, manage, and apply 
sensitive word filtering rules for WeChat Work applications.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Entities](#entities)
    - [InterceptRule Entity](#interceptrule-entity)
    - [InterceptType Enum](#intercepttype-enum)
  - [Commands](#commands)
    - [Sync Intercept Rules](#sync-intercept-rules)
  - [API Requests](#api-requests)
  - [Repository](#repository)
  - [Event Listeners](#event-listeners)
- [Advanced Usage](#advanced-usage)
  - [Custom Rule Processing](#custom-rule-processing)
  - [Bulk Operations](#bulk-operations)
  - [Integration with External Systems](#integration-with-external-systems)
- [Database Schema](#database-schema)
- [Cron Job](#cron-job)
- [Dependencies](#dependencies)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)
- [References](#references)

## Features

- **Intercept Rule Management**: Create, update, and delete sensitive word rules
- **Automatic Synchronization**: Sync rules from WeChat Work API automatically
- **Flexible Rule Types**: Support for warning-only and intercept-and-block rule types
- **Cron Job Integration**: Automatic rule synchronization every 10 minutes
- **Multi-Agent Support**: Manage rules across multiple WeChat Work agents
- **Doctrine Integration**: Full ORM support with entities and repositories

## Installation

```bash
composer require tourze/wechat-work-intercept-rule-bundle
```

## Configuration

Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    WechatWorkInterceptRuleBundle\WechatWorkInterceptRuleBundle::class => ['all' => true],
];
```

## Usage

### Entities

#### InterceptRule Entity

The main entity for managing sensitive word rules:

```php
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;

// Create a new intercept rule
$rule = new InterceptRule();
$rule->setName('Marketing Keywords');
$rule->setWordList(['spam', 'promotion', 'advertisement']);
$rule->setInterceptType(InterceptType::WARN);
```

#### InterceptType Enum

Defines the action to take when sensitive words are detected:

- `InterceptType::WARN`: Warning and block sending (警告并拦截发送)
- `InterceptType::NOTICE`: Warning only (仅警告)

### Commands

#### Sync Intercept Rules

Synchronize sensitive word rules from WeChat Work API:

```bash
php bin/console wechat-work:sync-intercept-rule
```

This command:
- Fetches all intercept rules from WeChat Work API
- Creates or updates local rule entities
- Runs automatically every 10 minutes via cron job
- Supports multiple agents and corporations

### API Requests

The bundle provides request classes for WeChat Work API integration:

```php
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleListRequest;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleDetailRequest;
use WechatWorkInterceptRuleBundle\Request\AddInterceptRuleRequest;
use WechatWorkInterceptRuleBundle\Request\UpdateInterceptRuleRequest;
use WechatWorkInterceptRuleBundle\Request\DeleteInterceptRuleRequest;

// Get list of intercept rules
$request = new GetInterceptRuleListRequest();
$request->setAgent($agent);
$response = $workService->request($request);

// Get detailed rule information
$detailRequest = new GetInterceptRuleDetailRequest();
$detailRequest->setAgent($agent);
$detailRequest->setRuleId('rule_id');
$detail = $workService->request($detailRequest);
```

### Repository

Use the repository for database operations:

```php
use WechatWorkInterceptRuleBundle\Repository\InterceptRuleRepository;

// Inject the repository
public function __construct(
    private InterceptRuleRepository $ruleRepository
) {}

// Find rules by corporation
$rules = $this->ruleRepository->findBy(['corp' => $corp]);

// Find rule by remote rule ID
$rule = $this->ruleRepository->findOneBy([
    'corp' => $corp,
    'ruleId' => 'remote_rule_id'
]);
```

### Event Listeners

The bundle includes event subscribers for handling rule-related events:

```php
use WechatWorkInterceptRuleBundle\EventSubscriber\InterceptRuleListener;
```

## Advanced Usage

### Custom Rule Processing

You can create custom processors for handling specific rule types:

```php
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;

class CustomRuleProcessor
{
    public function processRule(InterceptRule $rule): bool
    {
        // Custom processing logic
        $wordList = $rule->getWordList();
        $interceptType = $rule->getInterceptType();
        
        // Implement your custom rule processing
        return $this->applyCustomLogic($wordList, $interceptType);
    }
}
```

### Bulk Operations

For managing large numbers of rules:

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

### Integration with External Systems

Example of integrating with external content filtering systems:

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
        
        // Send to external system
        $this->externalClient->updateRule($externalData);
    }
}
```

## Database Schema

The bundle creates the following table:

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

## Cron Job

The bundle automatically registers a cron job to sync rules every 10 minutes:

```php
#[AsCronTask(expression: '*/10 * * * *')]
```

## Dependencies

This bundle requires:

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- WeChat Work Bundle
- Various Tourze utility bundles

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/wechat-work-intercept-rule-bundle/tests
```

## Contributing

Contributions are welcome! Please submit pull requests or create issues for bugs and feature requests.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## References

- [WeChat Work API Documentation](https://developer.work.weixin.qq.com/document/path/96346)
- [Symfony Bundle Documentation](https://symfony.com/doc/current/bundles.html)
