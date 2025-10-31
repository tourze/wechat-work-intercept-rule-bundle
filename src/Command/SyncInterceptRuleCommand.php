<?php

declare(strict_types=1);

namespace WechatWorkInterceptRuleBundle\Command;

use Carbon\CarbonImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\DoctrineDirectInsertBundle\Service\DirectInsertService;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkInterceptRuleBundle\Entity\InterceptRule;
use WechatWorkInterceptRuleBundle\Enum\InterceptType;
use WechatWorkInterceptRuleBundle\Repository\InterceptRuleRepository;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleDetailRequest;
use WechatWorkInterceptRuleBundle\Request\GetInterceptRuleListRequest;

#[AsCronTask(expression: '*/10 * * * *')]
#[AsCommand(name: self::NAME, description: '同步敏感词规则')]
#[Autoconfigure(public: true)]
class SyncInterceptRuleCommand extends Command
{
    public const NAME = 'wechat-work:sync-intercept-rule';

    public function __construct(
        private readonly AgentRepository $agentRepository,
        private readonly InterceptRuleRepository $ruleRepository,
        private readonly WorkService $workService,
        private readonly DirectInsertService $directInsertService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->agentRepository->findAll() as $agent) {
            $this->processAgent($agent, $output);
        }

        return Command::SUCCESS;
    }

    private function processAgent(Agent $agent, OutputInterface $output): void
    {
        $request = new GetInterceptRuleListRequest();
        $request->setAgent($agent);

        $response = $this->workService->request($request);
        if (!is_array($response) || !isset($response['rule_list'])) {
            $corpName = $agent->getCorp()?->getName() ?? 'Unknown';
            $output->writeln("[{$corpName}] 拉取敏感词列表失败");
            return;
        }

        assert(is_array($response['rule_list']));
        foreach ($response['rule_list'] as $arr) {
            assert(is_array($arr));
            /** @var array<string, mixed> $arr */
            $this->processRule($arr, $agent);
        }
    }

    /**
     * @param array<string, mixed> $arr
     */
    private function processRule(array $arr, Agent $agent): void
    {
        assert(isset($arr['rule_id']));
        assert(is_string($arr['rule_id']));

        // 主动从远程接口拉取详情信息
        $request = new GetInterceptRuleDetailRequest();
        $request->setAgent($agent);
        $request->setRuleId($arr['rule_id']);
        $detailResponse = $this->workService->request($request);
        $detail = is_array($detailResponse) ? ($detailResponse['rule'] ?? []) : [];
        assert(is_array($detail));
        /** @var array<string, mixed> $detail */

        $item = $this->ruleRepository->findOneBy([
            'corp' => $agent->getCorp(),
            'ruleId' => $arr['rule_id'],
        ]);

        if (null === $item) {
            $this->createInterceptRule($arr, $detail, $agent);
        }
    }

    /**
     * @param array<string, mixed> $arr
     * @param array<string, mixed> $detail
     */
    private function createInterceptRule(array $arr, array $detail, Agent $agent): void
    {
        $item = new InterceptRule();
        $item->setCorp($agent->getCorp());
        $item->setAgent($agent);
        assert(isset($arr['rule_id']));
        assert(is_string($arr['rule_id']));
        $item->setRuleId($arr['rule_id']);

        $this->setBasicRuleInfo($item, $arr);
        $this->setWordList($item, $detail);
        $this->setApplicableRange($item, $detail);

        $item->setSync(true);
        $this->directInsertService->directInsert($item);
    }

    /**
     * @param array<string, mixed> $arr
     */
    private function setBasicRuleInfo(InterceptRule $item, array $arr): void
    {
        assert(isset($arr['create_time']));
        assert(is_int($arr['create_time']));
        $item->setCreateTime(CarbonImmutable::createFromTimestamp($arr['create_time'], date_default_timezone_get())->toDateTimeImmutable());

        assert(isset($arr['rule_name']));
        assert(is_string($arr['rule_name']));
        $item->setName($arr['rule_name']);
    }

    /**
     * @param array<string, mixed> $detail
     */
    private function setWordList(InterceptRule $item, array $detail): void
    {
        $wordList = $detail['word_list'] ?? [];
        assert(is_array($wordList));
        $item->setWordList($this->convertToStringArray($wordList));

        $interceptType = $detail['intercept_type'] ?? '';
        assert(is_string($interceptType) || is_int($interceptType));
        $item->setInterceptType(InterceptType::tryFrom($interceptType));
    }

    /**
     * @param array<string, mixed> $detail
     */
    private function setApplicableRange(InterceptRule $item, array $detail): void
    {
        $applicableRange = $detail['applicable_range'] ?? [];
        assert(is_array($applicableRange));

        $userList = $applicableRange['user_list'] ?? [];
        assert(is_array($userList));
        $item->setApplicableUserList($this->convertToStringArray($userList));

        $departmentList = $applicableRange['department_list'] ?? [];
        assert(is_array($departmentList));
        $item->setApplicableDepartmentList($this->convertToIntArray($departmentList));
    }

    /**
     * @param array<mixed> $list
     * @return array<string>
     */
    private function convertToStringArray(array $list): array
    {
        $result = [];
        foreach ($list as $item) {
            if (is_string($item)) {
                $result[] = $item;
            } elseif (is_numeric($item)) {
                $result[] = (string) $item;
            } else {
                $result[] = '';
            }
        }
        return $result;
    }

    /**
     * @param array<mixed> $list
     * @return array<int>
     */
    private function convertToIntArray(array $list): array
    {
        $result = [];
        foreach ($list as $item) {
            if (is_int($item)) {
                $result[] = $item;
            } elseif (is_numeric($item)) {
                $result[] = (int) $item;
            } else {
                $result[] = 0;
            }
        }
        return $result;
    }
}
