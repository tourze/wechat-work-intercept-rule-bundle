<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkInterceptRuleBundle\Controller\Admin\InterceptRuleCrudController;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;

/**
 * InterceptRuleCrudController 测试用例
 *
 * 测试敏感词规则CRUD控制器的功能
 *
 * @internal
 */
#[CoversClass(InterceptRuleCrudController::class)]
#[RunTestsInSeparateProcesses]
final class InterceptRuleCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    private function getController(): InterceptRuleCrudController
    {
        return new InterceptRuleCrudController();
    }

    protected function getControllerService(): InterceptRuleCrudController
    {
        return new InterceptRuleCrudController();
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'header_id' => ['ID'];
        yield 'header_corp' => ['企业'];
        yield 'header_app' => ['应用'];
        yield 'header_rule_name' => ['规则名称'];
        yield 'header_sensitive_words' => ['敏感词列表'];
        yield 'header_intercept_type' => ['拦截方式'];
        yield 'header_sync_status' => ['同步状态'];
        yield 'header_created_at' => ['创建时间'];
        yield 'header_updated_at' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'field_name' => ['name'];
        yield 'field_sync' => ['sync'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'field_rule_id' => ['ruleId'];
        yield 'field_name' => ['name'];
        yield 'field_sync' => ['sync'];
    }

    public function testGetEntityFqcn(): void
    {
        self::assertSame(InterceptRule::class, InterceptRuleCrudController::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $fields = $this->getController()->configureFields(Crud::PAGE_INDEX);

        $fieldsArray = iterator_to_array($fields);
        self::assertNotEmpty($fieldsArray);

        // 检查字段数量是否合理
        self::assertGreaterThan(5, count($fieldsArray), '应该有多个字段配置');

        // 检查所有字段都是FieldInterface的实例
        foreach ($fieldsArray as $field) {
            self::assertInstanceOf(FieldInterface::class, $field);
        }
    }

    public function testConfigureFieldsForNewPage(): void
    {
        $fields = $this->getController()->configureFields(Crud::PAGE_NEW);

        $fieldsArray = iterator_to_array($fields);
        self::assertNotEmpty($fieldsArray);
    }

    public function testConfigureFieldsForEditPage(): void
    {
        $fields = $this->getController()->configureFields(Crud::PAGE_EDIT);

        $fieldsArray = iterator_to_array($fields);
        self::assertNotEmpty($fieldsArray);
    }

    public function testConfigureFieldsForDetailPage(): void
    {
        $fields = $this->getController()->configureFields(Crud::PAGE_DETAIL);

        $fieldsArray = iterator_to_array($fields);
        self::assertNotEmpty($fieldsArray);
    }

    public function testBusinessScenarioAdminManagement(): void
    {
        // 业务场景：管理员管理敏感词规则
        $entityFqcn = InterceptRuleCrudController::getEntityFqcn();
        self::assertSame(InterceptRule::class, $entityFqcn);

        // 验证配置方法可以正常调用
        $crud = $this->getController()->configureCrud(Crud::new());
        $filters = $this->getController()->configureFilters(Filters::new());

        // 通过能够正常执行到这里来验证配置方法工作正常
    }

    public function testBusinessScenarioRuleConfiguration(): void
    {
        // 业务场景：配置敏感词规则
        $fields = $this->getController()->configureFields(Crud::PAGE_NEW);

        $fieldsArray = iterator_to_array($fields);
        self::assertNotEmpty($fieldsArray);

        // 验证字段配置合理
        self::assertGreaterThan(5, count($fieldsArray), '新建页面应该有足够的字段供配置');

        foreach ($fieldsArray as $field) {
            self::assertInstanceOf(FieldInterface::class, $field);
        }
    }

    public function testBusinessScenarioRuleMonitoring(): void
    {
        // 业务场景：监控规则同步状态
        $fields = $this->getController()->configureFields(Crud::PAGE_INDEX);

        $fieldsArray = iterator_to_array($fields);

        // 验证索引页面字段配置
        self::assertNotEmpty($fieldsArray, '索引页面应该有字段显示');

        foreach ($fieldsArray as $field) {
            self::assertInstanceOf(FieldInterface::class, $field);
        }
    }

    /**
     * 测试表单验证错误 - 验证必填字段的验证规则
     */
    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 访问新建表单页面
        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        $this->assertResponseIsSuccessful();

        // 获取表单并提交空表单以触发验证错误
        $form = $crawler->selectButton('Create')->form();

        // 提交空表单
        $crawler = $client->submit($form);

        // 验证返回422状态码（表单验证错误）
        $this->assertResponseStatusCodeSame(422);

        // 检查验证错误信息
        $invalidFeedbackElements = $crawler->filter('.invalid-feedback, .form-error-message, .form-help');

        if ($invalidFeedbackElements->count() > 0) {
            $errorText = $invalidFeedbackElements->text();

            // 验证包含必填字段的验证错误信息
            $this->assertTrue(
                str_contains($errorText, 'should not be blank')
                || str_contains($errorText, '不能为空')
                || str_contains($errorText, 'This value should not be blank')
                || str_contains($errorText, 'This field is required'),
                '应该包含必填字段验证错误信息，实际得到: ' . $errorText
            );
        } else {
            // 如果没有找到错误元素，检查整个响应内容
            $response = $client->getResponse();
            $responseContent = $response->getContent();

            if (false !== $responseContent) {
                $this->assertTrue(
                    str_contains($responseContent, 'should not be blank')
                    || str_contains($responseContent, '不能为空')
                    || str_contains($responseContent, 'This value should not be blank')
                    || str_contains($responseContent, 'form-error')
                    || str_contains($responseContent, 'invalid-feedback'),
                    '响应中应该包含表单验证错误信息'
                );
            } else {
                self::fail('无法获取响应内容');
            }
        }
    }
}
