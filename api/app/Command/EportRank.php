<?php

declare(strict_types=1);

namespace App\Command;


use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Di\Annotation\Inject;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use function Taoran\HyperfPackage\Helpers\get_msectime;
use function Taoran\HyperfPackage\Helpers\getDateByInterval;
use function Taoran\HyperfPackage\Helpers\set_save_data;

/**
 * @Command
 */
#[Command]
class EportRank extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('export:rank');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('export data');
    }

    public function handle()
    {
        $file = 'storage/hzw_001.csv';
        $this->exportCsv($file, [
            'other' => '海贼王',
        ], '2011-01-01', '2022-04-25');
    }

    public function exportCsv($file, $params, $startDate, $endDate, $type = 'week')
    {
        $dates = getDateByInterval($startDate ,$endDate, $type);

        $file = fopen($file, 'w');

        $names = \App\Model\Rank::groupBy('name');
        // condition build
        $this->conditionBuild($names, $params);
        $names = $names->pluck('name');
        //build csv
        fputcsv($file, ['name', 'other', 'date', 'value']);
        $names->each(function ($item) use ($dates, $file) {
            foreach ($dates as $val) {
                $sum = \App\Model\Rank::where('is_on', 1)
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

        return true;
    }

    /**
     * condition build
     *
     * @param $model
     * @param $params
     */
    public function conditionBuild($model, $params)
    {
        if (isset($params['other']) && $params['other'] != '') {
            $model->where('other', $params['other']);
        }

        if (isset($params['name']) && $params['name'] != '') {
            $model->where('name', $params['name']);
        }

        if (isset($params['type']) && $params['type'] != '') {
            $model->where('type', $params['type']);
        }
    }
}
