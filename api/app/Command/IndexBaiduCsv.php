<?php

declare(strict_types=1);

namespace App\Command;

use App\Packages\DataInterface\src\BaiduIndex;
use App\Packages\DataInterface\src\Export;
use App\Service\RankService;
use Carbon\Carbon;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Context\Context;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Coroutine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Input\InputArgument;
use function Taoran\HyperfPackage\Helpers\getDateByInterval;
use function Taoran\HyperfPackage\Helpers\set_save_data;
use Hyperf\Di\Annotation\Inject;
/**
 * @Command
 */
#[Command]
class IndexBaiduCsv extends HyperfCommand
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

        parent::__construct('index:baidu_csv');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('百度指数csv');
    }

    public function handle()
    {
        //$this->rankService->genCsv('./202201_anime_rank.csv', '2022-01-01', '2022-04-09');

        $word = [
            [
                [
                    'name' => '乔巴',
                    'wordType' => 1
                ]
            ],
            [
                [
                    'name' => '索隆',
                    'wordType' => 1
                ]
            ],
            /*[
                [
                    'name' => '娜美',
                    'wordType' => 1
                ]
            ],
            [
                [
                    'name' => '乌索普',
                    'wordType' => 1
                ]
            ],
            [
                [
                    'name' => '山治',
                    'wordType' => 1
                ]
            ],*/
        ];
        //日期
        $startDate = '2011-01-01';
        $endDate = '2022-04-23';
        //地区
        $area = 0;
        $cookie = 'BIDUPSID=DB2E1EE7D6DB6C2F0D8208A6ED4B25E5; PSTM=1634463614; HMACCOUNT=EA9C0855E47A4A1B; HMACCOUNT_BFESS=EA9C0855E47A4A1B; __yjs_duid=1_e14917d477c6fd337756e23ea9d63eac1634484789836; MCITY=-257%3A; BAIDUID=6436371E5C3CAFD0165327E32CF5746E:SL=0:NR=10:FG=1; BDUSS=WtVTGhYemVTSU1sLVhGaWQtdkxadzFuSUxsOVFLZzNRdzZodzFTV2R1NVllbnBpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAFjtUmJY7VJib; BDUSS_BFESS=WtVTGhYemVTSU1sLVhGaWQtdkxadzFuSUxsOVFLZzNRdzZodzFTV2R1NVllbnBpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAFjtUmJY7VJib; BDORZ=B490B5EBF6F3CD402E515D22BCDA1598; BDSFRCVID=ocCOJexroG0leprDjurVMeX1vQpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF=tR30WJbHMTrDHJTg5DTjhPrMhUbmWMT-MTryKKJs54JKsbcGKljYbqvL5RQjLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-DKwDT853e; bdindexid=f5sau575d0bln8rea3fkguqju7; ab_sr=1.0.1_M2QzNDNkOWQ1ZWY5NDg0ODNiNTg0OTllNDAzZGRjN2JhNGQ4YjQwNTI1Y2UxN2E5MzgzMDI1YmM4NWEwY2RkZWExMWExNWE5YmJiOTZlNTQ1ODVhNWRjYWY2NjU1ZDU3ZTQ5YjM0YjA1ZDE0ZTMzNGM0NGJhMTA4Nzk2OTE3OWU0NzEzYmRiY2YxNmY0YWZiODMxNTlhNzg1ZWZmMThkMw==; H_PS_PSSID=36309_31254_34813_36166_34584_36121_36341_36075_26350_36349_36061; delPer=0; PSINO=7; BDSFRCVID_BFESS=ocCOJexroG0leprDjurVMeX1vQpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF_BFESS=tR30WJbHMTrDHJTg5DTjhPrMhUbmWMT-MTryKKJs54JKsbcGKljYbqvL5RQjLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-DKwDT853e; RT="sl=u&ss=l2evw0ry&tt=oj5&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&z=1&dm=baidu.com&si=ob6okjy6nnl&ld=5k41&cl=1u31"';
        $yearScope = getDateByInterval($startDate, $endDate, 'year');

        $baiduIndex = new BaiduIndex();
        $export = new Export();
        foreach ($yearScope as $key => $val) {
            $result = $baiduIndex->setCookie($cookie)->search($word, $val['startDate'], $val['endDate'], $area);

            $export->csv('storage/23-20220423.csv', $result, ($key > 0) ? false : true);

            sleep(3);
        }
        return true;
    }
}
