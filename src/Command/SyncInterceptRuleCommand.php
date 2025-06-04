<?php

namespace WechatWorkInterceptRuleBundle\Command;

use Carbon\Carbon;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;
use WechatWorkInterceptRuleBundle\Repository\InterceptRuleRepository;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleDetailRequest;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleListRequest;

#[AsCronTask('*/10 * * * *')]
#[AsCommand(name: 'wechat-work:sync-intercept-rule', description: '同步敏感词规则')]
class SyncInterceptRuleCommand extends Command
{
    public function __construct(
        private readonly AgentRepository $agentRepository,
        private readonly InterceptRuleRepository $ruleRepository,
        private readonly WorkService $workService,
        private readonly DoctrineService $doctrineService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->agentRepository->findAll() as $agent) {
            $request = new GetInterceptRuleListRequest();
            $request->setAgent($agent);

            $response = $this->workService->request($request);
            if (!isset($response['rule_list'])) {
                $output->writeln("[{$agent->getCorp()->getName()}] 拉取敏感词列表失败");
                continue;
            }

            foreach ($response['rule_list'] as $arr) {
                // 主动从远程接口拉取详情信息
                $request = new GetInterceptRuleDetailRequest();
                $request->setAgent($agent);
                $request->setRuleId($arr['rule_id']);
                $detail = $this->workService->request($request)['rule'];

                $item = $this->ruleRepository->findOneBy([
                    'corp' => $agent->getCorp(),
                    'ruleId' => $arr['rule_id'],
                ]);
                if (!$item) {
                    $item = new InterceptRule();
                    $item->setCorp($agent->getCorp());
                    $item->setAgent($agent);
                    $item->setRuleId($arr['rule_id']);
                    $item->setCreateTime(Carbon::createFromTimestamp($arr['create_time'], date_default_timezone_get()));
                    $item->setName($arr['rule_name']);
                    $item->setWordList($detail['word_list']);
                    $item->setInterceptType(InterceptType::tryFrom($detail['intercept_type']));
                    $item->setApplicableUserList($detail['applicable_range']['user_list']);
                    $item->setApplicableDepartmentList($detail['applicable_range']['department_list']);
                    $item->setSync(true);
                    $this->doctrineService->directInsert($item); // 直接插入
                }
            }
        }

        return Command::SUCCESS;
    }
}
