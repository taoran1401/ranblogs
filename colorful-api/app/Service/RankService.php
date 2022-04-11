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
        $baiduIndex = new \App\Packages\DataInterface\src\BiaduIndex();
        $baiduIndex->loadMysql($params, new \App\Model\Rank());
        return true;
    }

    /**
     * 按日生成csv
     *
     * @param $file
     * @param $startDate
     * @param $endDate
     * @param array|null $names
     * @return bool
     */
    public function genCsvByDay($file, $startDate, $endDate, array $names = null)
    {
        $file = fopen($file, 'w');

        fputcsv($file, ['name', 'date', 'value']);
        if (!empty($names)) {
            $rank = Db::table('rank')->where('is_on', 1)
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->whereIn('name', $names)
                ->orderBy('date', 'asc')
                ->get();
            $rank->each(function ($item) use ($file) {
                fputcsv($file, [$item->name, $item->date, $item->value]);
            });
        } else {
            $rank = Db::table('rank')->where('is_on', 1)
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->orderBy('date', 'asc')
                ->get();
            $rank->each(function ($item) use ($file) {
                fputcsv($file, [$item->name, $item->date, $item->value]);
            });
        }

        fclose($file);
        return true;
    }

    public function genCsvByWeek($file, $startDate, $endDate)
    {
        $dates = getDateByInterval($startDate ,$endDate, 'week');

        $file = fopen($file, 'w');
        if (!$file) {
            throw new \Exception('文件');
        }

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
    }
}