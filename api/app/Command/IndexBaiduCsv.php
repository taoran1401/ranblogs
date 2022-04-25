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
                //组合词
                [
                    'name' => 'php',
                    'wordType' => 1
                ],
                [
                    'name' => 'golang',
                    'wordType' => 1
                ]
            ],
            [
                //单个
                [
                    'name' => 'java',
                    'wordType' => 1
                ]
            ],
            [
                //单个
                [
                    'name' => 'python',
                    'wordType' => 1
                ]
            ],
            [
                //单个
                [
                    'name' => 'javascript',
                    'wordType' => 1
                ]
            ],
            [
                //单个
                [
                    'name' => 'c#',
                    'wordType' => 1
                ]
            ],
        ];
        //日期
        $startDate = '2021-03-01';
        $endDate = '2021-06-23';
        //地区
        $area = 0;
        $cookie = 'PSTM=1619529893; BIDUPSID=A7911A2514EB4CC3EE1425BEEC0979CC; __yjs_duid=1_edf34bb9feb9f05b03d967cb836def6f1619967340911; H_WISE_SIDS=107318_110085_127969_153067_179345_184716_185634_189256_189755_191067_192206_192391_192408_193284_194085_194512_194520_195329_195342_195468_196049_196428_196514_196528_197242_197711_197782_197958_198120_198254_198929_199022_199082_199467_199490_199574_200736_200763_200958_201054_201107_201187_201233_201361_201541_201556_201576_201705_201978_201996_202058_202111_202565_202652_202760_202904_202910_203197_203250_203267_203542_203606_203629_204032_204099_204102_204112_204131_204154_204205_204262_204371_204432; MCITY=-289%3A; BAIDUID=0387E1D3A675D372665603DAD7A82B26:SL=0:NR=10:FG=1; BDORZ=B490B5EBF6F3CD402E515D22BCDA1598; BAIDUID_BFESS=0387E1D3A675D372665603DAD7A82B26:SL=0:NR=10:FG=1; BA_HECTOR=218l04aga4800la4q91h69vdo0r; uc_login_unique=1234358d7a251af63537ad425e136d03; uc_recom_mark=cmVjb21tYXJrXzM3MzAyOTQx; delPer=0; PSINO=6; BCLID=7732568761536106711; BDSFRCVID=A4kOJexroG0leprDjCbTMeKNyrpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF=tR30WJbHMTrDHJTg5DTjhPrMLHOWWMT-MTryKKJs54JKsbcJ-lOYbfPN2q5jLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-D8wj6-h3e; BCLID_BFESS=7732568761536106711; BDSFRCVID_BFESS=A4kOJexroG0leprDjCbTMeKNyrpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF_BFESS=tR30WJbHMTrDHJTg5DTjhPrMLHOWWMT-MTryKKJs54JKsbcJ-lOYbfPN2q5jLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-D8wj6-h3e; Hm_lvt_d101ea4d2a5c67dab98251f0b5de24dc=1649218125,1649324141,1649389757,1650854518; BDUSS=NDZ2FyT3lmdktRcEdhTDMta1JnZk0zSUZXdTI1azg2UHJMdVVRM2dEWU5tSTFpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA0LZmINC2ZiOF; BDUSS_BFESS=NDZ2FyT3lmdktRcEdhTDMta1JnZk0zSUZXdTI1azg2UHJMdVVRM2dEWU5tSTFpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA0LZmINC2ZiOF; CHKFORREG=27eec5721079530a30ebcd4cc39faa47; Hm_up_d101ea4d2a5c67dab98251f0b5de24dc=%7B%22uid_%22%3A%7B%22value%22%3A%221433106078%22%2C%22scope%22%3A1%7D%7D; bdindexid=fk6jlrhvai9488sqpeem62g874; Hm_lpvt_d101ea4d2a5c67dab98251f0b5de24dc=1650854947; ab_sr=1.0.1_NzY4NDVkMjhlNWQwNWJhMDcwZjE3MzViZjgyMzQwY2Q3NDMxYTgzNTgwNTk1YTdmMDkxODAyOTRlYzViNDNkM2FjZjI3NzM1ZjMwOGM4NGU4YTM0ODU5ZmVmMzNkYzcyNTVjOTRhNGEwZThkYjdkODZkMjJjZThhNjJmODgxNjJjYzQxOWI5YjMzMzFiOWU4MWUyYWUzYTE5Zjk3NDAyNQ==; __yjs_st=2_MzIwN2U3ZWJlY2Y4MjI4NzU4ZjI0ZjkxNGNjOWJkZjg5YThmN2IzNzc2YTFiNjRkNzRiOTIxZmU3ZDdkNGI3ODM1ZGViNTFjMjQyZmZiMWI5MDhmMDRhYmNhYWRlMjY3OTgzN2E4OGNlMWYxMGViNzhiNTRiOTdiYzQ1MTM0OGQ3OWI0MjhjYWE2MmNiMDVjYTY3NGNmYmQzMDNkNzA3YjgwYzJkNTRlMzI2NzhhMzI1OWJiYmM5ZjM5ZjBlYmEwXzdfZmNjNGY3NWM=; H_PS_PSSID=36309_31660_35912_36166_34584_36121_36342_36075_36297_36344_26350_36349_36061; RT="z=1&dm=baidu.com&si=rnjh6fung9b&ss=l2e45lqq&sl=i&tt=cav&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&ld=luuk"';
        $yearScope = getDateByInterval($startDate, $endDate, 'year');

        $baiduIndex = new BaiduIndex();
        $export = new Export();
        foreach ($yearScope as $key => $val) {
            $result = $baiduIndex->setCookie($cookie)->search($word, $val['startDate'], $val['endDate'], $area);

            $export->csv('storage/test.csv', $result, ($key > 0) ? false : true);

            sleep(3);
        }
        return true;
    }
}
