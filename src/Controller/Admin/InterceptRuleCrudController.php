<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;

#[AdminCrud(routePath: '/wechat-work/intercept-rule', routeName: 'wechat_work_intercept_rule')]
final class InterceptRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InterceptRule::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('敏感词规则')
            ->setEntityLabelInPlural('敏感词规则')
            ->setPageTitle('index', '敏感词规则管理')
            ->setPageTitle('new', '新建敏感词规则')
            ->setPageTitle('edit', '编辑敏感词规则')
            ->setPageTitle('detail', '敏感词规则详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->showEntityActionsInlined()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('corp'))
            ->add(EntityFilter::new('agent'))
            ->add('name')
            ->add(BooleanFilter::new('sync'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
        ;

        yield AssociationField::new('corp', '企业')
            ->setRequired(true)
            ->setHelp('选择此规则适用的企业')
        ;

        yield AssociationField::new('agent', '应用')
            ->setRequired(true)
            ->setHelp('选择此规则适用的企业微信应用')
        ;

        yield TextField::new('ruleId', '规则ID')
            ->hideOnIndex()
            ->setHelp('企业微信系统中的规则ID，同步后自动填充')
        ;

        yield TextField::new('name', '规则名称')
            ->setRequired(true)
            ->setMaxLength(20)
            ->setHelp('敏感词规则的名称，最多20个字符')
        ;

        yield ArrayField::new('wordList', '敏感词列表')
            ->setRequired(true)
            ->setHelp('需要拦截的敏感词列表，每行一个词语')
        ;

        yield ArrayField::new('semanticsList', '语义规则')
            ->hideOnIndex()
            ->setHelp('额外的语义拦截规则（如：电话号码、邮箱等）')
        ;

        $interceptTypeField = EnumField::new('interceptType', '拦截方式')
            ->setRequired(true)
        ;
        $interceptTypeField->setEnumCases(InterceptType::cases());
        $interceptTypeField->setHelp('选择拦截方式：警告并拦截发送 或 仅发警告');
        yield $interceptTypeField;

        yield ArrayField::new('applicableUserList', '适用用户')
            ->hideOnIndex()
            ->setHelp('此规则适用的用户ID列表，为空则适用于所有用户')
        ;

        yield ArrayField::new('applicableDepartmentList', '适用部门')
            ->hideOnIndex()
            ->setHelp('此规则适用的部门ID列表，为空则适用于所有部门')
        ;

        yield BooleanField::new('sync', '同步状态')
            ->renderAsSwitch(false)
            ->setHelp('是否已同步到企业微信服务器')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield AssociationField::new('createdBy', '创建者')
            ->hideOnIndex()
            ->hideOnForm()
        ;

        yield AssociationField::new('updatedBy', '更新者')
            ->hideOnIndex()
            ->hideOnForm()
        ;
    }
}
