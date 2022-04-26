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
                    'name' => '弗兰奇',
                    'wordType' => 1
                ]
            ]
        ];

        //日期
        $startDate = '2011-01-01';
        $endDate = '2022-04-25';
        //地区
        $area = 0;
        $cookie = 'PSTM=1619529893; BIDUPSID=A7911A2514EB4CC3EE1425BEEC0979CC; HMACCOUNT=425DADBA6957933F; HMACCOUNT_BFESS=425DADBA6957933F; __yjs_duid=1_edf34bb9feb9f05b03d967cb836def6f1619967340911; H_WISE_SIDS=107318_110085_127969_153067_179345_184716_185634_189256_189755_191067_192206_192391_192408_193284_194085_194512_194520_195329_195342_195468_196049_196428_196514_196528_197242_197711_197782_197958_198120_198254_198929_199022_199082_199467_199490_199574_200736_200763_200958_201054_201107_201187_201233_201361_201541_201556_201576_201705_201978_201996_202058_202111_202565_202652_202760_202904_202910_203197_203250_203267_203542_203606_203629_204032_204099_204102_204112_204131_204154_204205_204262_204371_204432; MCITY=-289%3A; BAIDUID=0387E1D3A675D372665603DAD7A82B26:SL=0:NR=10:FG=1; BDORZ=B490B5EBF6F3CD402E515D22BCDA1598; BDUSS=NDZ2FyT3lmdktRcEdhTDMta1JnZk0zSUZXdTI1azg2UHJMdVVRM2dEWU5tSTFpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA0LZmINC2ZiOF; BDUSS_BFESS=NDZ2FyT3lmdktRcEdhTDMta1JnZk0zSUZXdTI1azg2UHJMdVVRM2dEWU5tSTFpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA0LZmINC2ZiOF; bdindexid=fk6jlrhvai9488sqpeem62g874; BA_HECTOR=agah8h248h80al2kba1h6ej8l0r; BAIDUID_BFESS=0387E1D3A675D372665603DAD7A82B26:SL=0:NR=10:FG=1; BDRCVFR[feWj1Vr5u3D]=I67x6TjHwwYf0; delPer=0; PSINO=6; H_PS_PSSID=36309_31660_35912_36166_34584_36121_36342_36075_36297_36344_26350_36349_36061; BCLID=7174626307158764202; BDSFRCVID=sNkOJexroG0leprDjnv0MeKN7QpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF=tR30WJbHMTrDHJTg5DTjhPrMKPTWWMT-MTryKKJs54JKsbck563YbfPNjnQjLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-DLGD6L53e; BCLID_BFESS=7174626307158764202; BDSFRCVID_BFESS=sNkOJexroG0leprDjnv0MeKN7QpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF_BFESS=tR30WJbHMTrDHJTg5DTjhPrMKPTWWMT-MTryKKJs54JKsbck563YbfPNjnQjLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-DLGD6L53e; ab_sr=1.0.1_YmRjOWYzMDA0Nzk2NGY2ZjA4N2NhZjFiZjc0MzYyYmIxOGZkYzY2NTEyOGU1M2RhZjhhMWQ2Y2Y5ODVlMmUzMDczZmJlNjdhYjMzMmJjMWRmYmJlNjUwNGQ4NmU4MWYxMjliYTc1NjJmNGM3NDA1ZGZiMTU5ZmFiZDQ4YTVhYmVlMDhiZGZlOGFhMzJmY2I2YzU1NDI5YjhlNWRmMWU2Zg==; RT="z=1&dm=baidu.com&si=ylvvoxmyhy&ss=l2fikww8&sl=5&tt=3sq&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&ld=7of"';
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
