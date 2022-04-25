<?php


namespace App\Service;

use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Taoran\HyperfPackage\Core\AbstractController;
use function Taoran\HyperfPackage\Helpers\getDateByInterval;

class RankService extends AbstractController
{
    /**
     * @Inject()
     * @var \App\Model\Rank
     */
    protected $rankModel;

    /**
     * 百度指数数据写入
     */
    public function baiduIndexInsert($params)
    {
        if (isset($params['status']) && $params['status'] != 0) {
            throw new \Exception($params['message']);
        }
        //$baiduIndex = new \App\Packages\DataInterface\src\BiaduIndex();
        $this->loadMysql($params, new \App\Model\Rank());
        return true;
    }

    /**
     * 装载到mysql
     *
     * @param $content
     * @param $model
     */
    public function loadMysql($content, $model)
    {
        $content = $content['data']['userIndexes'];
        foreach ($content as $key => $val) {
            foreach ($val['all']['formatdata'] as $key => $_val) {
                $insert = [
                    'name' => $val['word'][0]['name'],
                    'other' => 'tmp',
                    'origin' => 1,
                    'type' => 0,
                    'date' => $_val['date'],
                    'value' => $_val['value'],
                ];

                $model->updateOrInsert(
                    ['date' => $insert['date'], 'name' => $insert['name'], 'origin' => $insert['origin'], 'type' => $insert['type']],
                    $insert
                );
            }
        }
    }

    /**
     * 生成csv(按日 - day，按周 - week，按月 - month, 按季度 - quarter, 按年 - year)
     *
     * @param $file
     * @param $startDate
     * @param $endDate
     * @param array|null $names
     * @param string $type
     * @return bool
     * @throws \Exception
     */
    public function genCsv($file, $startDate, $endDate, array $names = null, string $type = 'day')
    {
        $dates = getDateByInterval($startDate ,$endDate, $type);

        $file = fopen($file, 'w');

        if (!$file) {
            throw new \Exception('文件');
        }

        fputcsv($file, ['name', 'date', 'value']);

        $rank = Db::table('rank')->where('is_on', 1);

        if (!empty($names)) {
            $rank->whereIn('name', $names);
        }

        foreach ($dates as $val) {
            $rank = Db::table('rank')->where('is_on', 1)
                ->selectRaw('
                    `name`,
                    sum(`value`) as `value`
                ')
                ->where('date', '>=', $val['startDate'])
                ->where('date', '<=', $val['endDate'])
                ->whereIn('name', $names)
                ->groupBy('name')
                ->orderBy('date', 'asc')
                ->first();
            fputcsv($file, [$rank->name, $val['endDate'], $rank->value]);
        }

        fclose($file);

        return true;
    }
}