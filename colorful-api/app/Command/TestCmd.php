<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RankService;
use Carbon\Carbon;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Context\Context;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Coroutine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use function Taoran\HyperfPackage\Helpers\getDateByInterval;
use function Taoran\HyperfPackage\Helpers\set_save_data;
use Hyperf\Di\Annotation\Inject;
/**
 * @Command
 */
#[Command]
class TestCmd extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RankService
     */
    protected $rankService;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('test:cmd');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {

//        $this->rankService->genCsvByDay('./202201_anime_rank.csv', '2022-01-01', '2022-04-09');
//        $this->line('ok', 'info');
        exit;

        $file = '/disk2/www/wanqu/colorful-api/app/Packages/ETL/src/data/hot_rank.csv';

        //加载：csv, excel, json
        //$content = $this->extract($file);

        //转换(转换成数据库格式，csv, excel, 数组)
        //$this->transform($content);

        //加载到数据仓库
        //$this->load($content, new \App\Model\Rank());

        $dates = getDateByInterval('2011-01-01' ,'2022-04-04', 'week');

        $file = fopen('./hot_week.csv', 'w');

        $names = Db::table('rank')->groupBy('name')->pluck('name');
        fputcsv($file, ['name', 'other', 'date', 'value']);
        $names->each(function ($item) use ($dates, $file) {
            foreach ($dates as $val) {
                $sum = Db::table('rank')->where('is_on', 1)
                    ->where('date', '>=', $val['startDate'])
                    ->where('date', '<=', $val['endDate'])
                    ->where('name', $item)
                    ->groupBy('name')
                    ->orderBy('date', 'asc')
                    ->sum('value');
                fputcsv($file, [$item, '', $val['endDate'], $sum]);
            }
        });

        fclose($file);

        var_dump('ok');
        exit;
//        var_dump($date);exit;
//        $result = getDateByInterval($startTime, $endTime, 'week');


        //@file_put_contents($writePath, $formatData, FILE_APPEND);
        var_dump($list->toArray());
        exit;
        var_dump($content);
    }

    public function transform($content, $type = 'db')
    {
        $fputcsv = '';
    }

    /**
     * 从csv中提取数据
     *
     * @param $file
     * @return array|string
     */
    public function extractFromCsv($file)
    {
        //文件验证
        $ext = $this->getExt($file);
        if ($ext != 'csv') {
            return '文件格式错误！';
        }

        //获取内容
        $content = [];
        $file = fopen($file, "r");
        while (!feof($file)) {
            $content[] = fgetcsv($file);
        }
        fclose($file);

        return $content;
    }

    /**
     * 从mysql中提取数据
     */
    public function extractFromMysql()
    {

    }

    /**
     * 加载到mysql
     *
     * @param $content
     * @param $model
     */
    public function loadMysql($content, $model)
    {
        $field = $this->fieldMap($content[0]);
        unset($content[0]);
        foreach ($content as $key => $val) {
            $val['origin'] = 1;
            $insert = array_combine($field, $val);
            $model->updateOrInsert(
                ['date' => $insert['date'], 'name' => $insert['name'], 'origin' => $insert['origin']],
                $insert
            );
        }
    }

    /**
     * 加载到csv
     *
     * @param $content
     * @param $fieldMap
     */
    public function loadCsv($content, $fieldMap)
    {

    }

    /**
     * 字段映射
     */
    public function fieldMap($map)
    {
        $map[1] = 'other';
        return $map;
    }

    public function getExt($file)
    {
        $ext = explode('.', $file);
        return $ext[count($ext) - 1];
    }
}
