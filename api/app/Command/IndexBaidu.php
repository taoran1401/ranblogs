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
class IndexBaidu extends HyperfCommand
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

        parent::__construct('index:baidu');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Index baidu statistics');
    }

    public function handle()
    {
        //$this->rankService->genCsv('./202201_anime_rank.csv', '2022-01-01', '2022-04-09');
        $otherField = '海贼王';
        $word = [
            [
                [
                    'name' => '桃之助',
                    'wordType' => 1
                ]
            ]
        ];

        //日期
        $startDate = '2011-01-01';
        // $endDate = '2012-12-31';
        $endDate = '2022-04-25';
        //地区
        $area = 0;
        $cookie = 'BIDUPSID=DB2E1EE7D6DB6C2F0D8208A6ED4B25E5; PSTM=1634463614; __yjs_duid=1_e14917d477c6fd337756e23ea9d63eac1634484789836; MCITY=-257%3A; BDUSS=WtVTGhYemVTSU1sLVhGaWQtdkxadzFuSUxsOVFLZzNRdzZodzFTV2R1NVllbnBpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAFjtUmJY7VJib; BDUSS_BFESS=WtVTGhYemVTSU1sLVhGaWQtdkxadzFuSUxsOVFLZzNRdzZodzFTV2R1NVllbnBpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAFjtUmJY7VJib; Hm_up_d101ea4d2a5c67dab98251f0b5de24dc=%7B%22uid_%22%3A%7B%22value%22%3A%221433106078%22%2C%22scope%22%3A1%7D%7D; BDORZ=B490B5EBF6F3CD402E515D22BCDA1598; BAIDUID=F26DCC3AF9809EEF6EAB155880DF818C:SL=0:NR=10:FG=1; BAIDUID_BFESS=F26DCC3AF9809EEF6EAB155880DF818C:SL=0:NR=10:FG=1; BA_HECTOR=24208k8lah818l2g491h72d9s0r; bdindexid=jnsm9dfcv9qo89cu1soe9a8ri0; BDRCVFR[feWj1Vr5u3D]=I67x6TjHwwYf0; delPer=0; PSINO=6; BCLID=10873142236181569283; BDSFRCVID=x2LOJexroG0leprDtREIrZTUcQpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF=tR30WJbHMTrDHJTg5DTjhPrM2a5rWMT-MTryKKJs54JKs-b-QfrKWl0A-nQjLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-D8CjT-h3e; BCLID_BFESS=10873142236181569283; BDSFRCVID_BFESS=x2LOJexroG0leprDtREIrZTUcQpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF_BFESS=tR30WJbHMTrDHJTg5DTjhPrM2a5rWMT-MTryKKJs54JKs-b-QfrKWl0A-nQjLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-D8CjT-h3e; Hm_lvt_d101ea4d2a5c67dab98251f0b5de24dc=1650976287,1651302355,1651631090,1651662326; H_PS_PSSID=36309_36367_34812_36165_34584_35979_36340_36075_36281_36235_26350_36349_36312_36061; RT="z=1&dm=baidu.com&si=rb5dfkvv0f&ss=l2rh3qkp&sl=4&tt=2u4&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf"; Hm_lpvt_d101ea4d2a5c67dab98251f0b5de24dc=1651666230; ab_sr=1.0.1_MmQyMGNlM2VmZTg4MzUyMjE3NGE1ZWI0MzE5ZmEwMTIwZWI3ZmIxYzVhNDNlMTAwZjEwNDlkOTcyMmIwNzVmMGM1OTdkN2VhN2U1OTczMjgzZjJlM2ExYTE3NjM5MDc1NTc2OTdhMjlkODdkZWI2MjAxMDQwYjdiNmM1N2E0NjAwYjk2ZDdjN2U5NjM3ODhiZTY1YmEzMzk3MjEyZjYxOQ==; __yjs_st=2_ZDM3YWE1YTE4MTk3OTU2NGI2MTY3N2Y1ZWY0ZmE2ZTY3NmZjZjRhMzFjOWIxODY5MjVjMmViZTM0ZGQxMGVjY2VmZDc5MTlhNzQxNzNiNzk0ZjRkNGRmNmI1MjZjZDI5NjY1NWUxYzQxYjVmZjg4YzIyNmQ3YjgyYjJhMGIzNjc1Nzc2MjZlYTMyZDJlMjk5NzFkOWY0YmQ2YmQ0Y2VkYzQ1NzEyMDhkYWZjMWVlZjI2ZTcyOTYzZmQ4NjBiOWU1XzdfOGQyNjQzNmU=';
        $yearScope = getDateByInterval($startDate, $endDate, 'year');

        $baiduIndex = new BaiduIndex();
        $export = new Export();
        foreach ($yearScope as $key => $val) {
            $result = $baiduIndex->setCookie($cookie)->search($word, $val['startDate'], $val['endDate'], $area);

            //$export->csv('storage/23-20220423.csv', $result, ($key > 0) ? false : true);

            //Insert db rank
            $this->loadDBRank($result, 1, $otherField);

            sleep(3);
        }
        return true;
    }

    /**
     * load mysql
     *
     * @param $content
     * @param $model
     */
    public function loadDBRank($data, $origin = 1, $otherField = 'tmp')
    {
        $model = new \App\Model\Rank();
        foreach ($data as $val) {
            $formatData = $val['all']['formatData'];
            foreach ($formatData as $_val) {
                if ($_val['value'] == 0) {
                    continue;
                }
                $model->updateOrInsert(
                    ['date' => $_val['date'], 'name' => $_val['word']],
                    [
                        'name' => $_val['word'],
                        'date' => $_val['date'],
                        'value' => $_val['value'],
                        'other' => $otherField,
                        'origin' => $origin
                    ]
                );
            }
        }
    }

    /**
     * 字段映射
     */
    public function fieldMap($map)
    {
        $map[1] = 'other';
        return $map;
    }

    /**
     * 获取文件后缀
     *
     * @param $file
     * @return mixed
     */
    public function getFileExt($file)
    {
        $ext = explode('.', $file);
        return $ext[count($ext) - 1];
    }
}
