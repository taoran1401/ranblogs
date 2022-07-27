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
        $this->line('start');

        //new des
        $this->des();
        exit;
        $exec_start_time = time();
        //$this->rankService->genCsv('./202201_anime_rank.csv', '2022-01-01', '2022-04-09');
        $otherField = '';
        $word = [
            [
                [
                    'name' => '',
                    'wordType' => 1
                ]
            ]
        ];

        //日期
        $startDate = '2011-01-01';
        // $endDate = '2012-12-31';
        $endDate = '2022-04-30';
        //地区
        $area = 0;
        $cookie = 'PSTM=1619529893; BIDUPSID=A7911A2514EB4CC3EE1425BEEC0979CC; __yjs_duid=1_edf34bb9feb9f05b03d967cb836def6f1619967340911; H_WISE_SIDS=107318_110085_127969_153067_179345_184716_185634_189256_189755_191067_192206_192391_192408_193284_194085_194512_194520_195329_195342_195468_196049_196428_196514_196528_197242_197711_197782_197958_198120_198254_198929_199022_199082_199467_199490_199574_200736_200763_200958_201054_201107_201187_201233_201361_201541_201556_201576_201705_201978_201996_202058_202111_202565_202652_202760_202904_202910_203197_203250_203267_203542_203606_203629_204032_204099_204102_204112_204131_204154_204205_204262_204371_204432; MCITY=-289%3A; BAIDUID=0387E1D3A675D372665603DAD7A82B26:SL=0:NR=10:FG=1; BDUSS=nZhfn5iaXVNOFdaRm41SDY3VXQ2TkdMWGN2bDlXV3ByZ1Q3YWdvZjQ5a0RscEZpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMJamIDCWpie; BDUSS_BFESS=nZhfn5iaXVNOFdaRm41SDY3VXQ2TkdMWGN2bDlXV3ByZ1Q3YWdvZjQ5a0RscEZpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMJamIDCWpie; Hm_up_d101ea4d2a5c67dab98251f0b5de24dc=%7B%22uid_%22%3A%7B%22value%22%3A%221433106078%22%2C%22scope%22%3A1%7D%7D; BDORZ=B490B5EBF6F3CD402E515D22BCDA1598; BAIDUID_BFESS=0387E1D3A675D372665603DAD7A82B26:SL=0:NR=10:FG=1; BDRCVFR[feWj1Vr5u3D]=I67x6TjHwwYf0; delPer=0; PSINO=6; BA_HECTOR=200h848l200k21all71h76tk60q; H_PS_PSSID=36309_31660_35912_36166_34584_35978_36075_36297_36344_26350_36349_36061; BCLID=7943399794776560168; BDSFRCVID=iXCOJexroG0leprDtp2mJ6YHPQpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF=tR30WJbHMTrDHJTg5DTjhPrMMJ7mWMT-MTryKKJs54JKs-bxD4ckjlFn0bQjLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-D_xjjLa3e; BCLID_BFESS=7943399794776560168; BDSFRCVID_BFESS=iXCOJexroG0leprDtp2mJ6YHPQpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF_BFESS=tR30WJbHMTrDHJTg5DTjhPrMMJ7mWMT-MTryKKJs54JKs-bxD4ckjlFn0bQjLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-D_xjjLa3e; Hm_lvt_d101ea4d2a5c67dab98251f0b5de24dc=1650854518,1650939213,1651116266,1651735463; bdindexid=uml12dk6gt92f8n1i7v264b840; Hm_lpvt_d101ea4d2a5c67dab98251f0b5de24dc=1651735468; RT="z=1&dm=baidu.com&si=xpkxfgjadn&ss=l2sonbqy&sl=2&tt=1fg&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf"; ab_sr=1.0.1_ZGExNjE4YWQ5ZWFkMzdlZTdhNWY4YTIxNGJjNmUxMWY4OThmYjIxNDVmZTlkNzAwODBmMjQ0YWFkZTI4Y2QzMTc0NTQ5NzlmMDYxNzc2ZjQxZTIwNjgxY2FhMTIyMDQ5NjE5MjRlMWVhNDIzMjNjZmYyOGVmZTA2MTdjMzQ4ZjUyOTNjMjgyNjgzNWRmYTA5MjhhMWY0YjIyZTYwMTY0Mw==; __yjs_st=2_NjNkYzRkZTQ1MTZmMzUzMjY2ZmZjZGUyOGE2ODBjNzEwMTk4YTA1NjVjZDdhM2M0ODM0YWZlMzhjN2MxZjk0M2U3MTMzZDQxNGRjOWIyNzA1ZTUyOTBkMDMzNGMxNjQ0YWIxMTc0ODE3YTJjZDRmODBlYjc0Yzc5ZGQyMWUyY2U3NjAwNzM4MDBjMzZmNGMxZjhlMTVhNzRlZjgzMDc5MDA4NGFlNmQyNzI1NTVhNGUxNTFmODczMzU4YzcyMjZjXzdfOWM1NzQxOGU=';
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

        $elapsed_info = '耗时:' . (time() - $exec_start_time) . 's';
        $this->line('end；' . $elapsed_info);

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
        $save_data = [];
        foreach ($data as $val) {
            $formatData = $val['all']['formatData'];
            foreach ($formatData as $_val) {
                if ($_val['value'] > 0) {
                    $save_data[] = [
                        'date' => $_val['date'],
                        'name' => $_val['word'],
                        'value' => $_val['value'],
                        'other' => $otherField,
                        'origin' => $origin
                    ];
                    /*$model->updateOrInsert(
                        ['date' => $_val['date'], 'name' => $_val['word']],
                        [
                            'value' => $_val['value'],
                            'other' => $otherField,
                            'origin' => $origin
                        ]
                    );*/
                }
            }
        }
        $model->insert($save_data);
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

    public function des()
    {
        //uri: Interface/ptbk
        $ptbk = "fodztw.YSKWc0-b250%4,3+697-1.8";
        //uri: api/SearchApi/index
        $jsonData = "{\"status\":0,\"data\":{\"userIndexes\":[{\"word\":[{\"name\":\"php\",\"wordType\":1}],\"all\":{\"startDate\":\"2022-06-27\",\"endDate\":\"2022-07-26\",\"data\":\"fWWtwfSbWwfSd0wfob0wfoW.w0Wf0w0SWKwfbd0wfWfSwfootwfSdSwfo.tw0S.ow0ootwfWtSwfSf0wfSofwfSt.wfoddw0oKbw0ooSwfoSWwfSotwfSfSwfSWSwftWKw0S.bw0S.fwfoffwfW.d\"},\"pc\":{\"startDate\":\"2022-06-27\",\"endDate\":\"2022-07-26\",\"data\":\"0bbtw0WK0w0Wfow0Wdbw0WdKwKtSwbWKw0K.bw0botw0Sbfw0W.0w0SbfwK0bwbS.w0bW0w0Wofw0Wb0w0WSSw0SotwK0WwbSdw0W0tw0WKow0WK.w0b.dw0StKwK0Kwbbtw0SWKw0bSS\"},\"wise\":{\"startDate\":\"2022-06-27\",\"endDate\":\"2022-07-26\",\"data\":\"bKdwbKSwbWSwbW.wbStwWWowbddwbS.wbWfwbWfwbWowbofwW0WwSK0wbWowbSKwbW0wbWWwbtSwSb0wSKSwbo.wboKwb..wbtSwb.dwW0KwWtbwbt.wbSt\"},\"type\":\"day\"},{\"word\":[{\"name\":\"java\",\"wordType\":1}],\"all\":{\"startDate\":\"2022-06-27\",\"endDate\":\"2022-07-26\",\"data\":\"0dW.ow00dobw0dS0tw0dboWw0d.ftwW0ffwSWW.w0dSd0w0dfSdw0doSfw0d0t.wK0SWwStKKwS0bfwKbdtwKStfwKo.WwKtSKwbt0twSddWwoKtSwKtfKwKoftwKffdwK0obwbSdWwSffSwS00bwKtofwKSWd\"},\"pc\":{\"startDate\":\"2022-06-27\",\"endDate\":\"2022-07-26\",\"data\":\"Sf0bwSWdfwSffdwSS..wS0tSw.f0owfbSdwS.towobotwSdSowoSKWwood0w.dSWwfWoowoKSWwoK.twoK00woWoSwodKowfbb0wfWf0wobfdwoKoKwoWdowoSbSwofWbw.dWfwfbodwoKt.wobto\"},\"wise\":{\"startDate\":\"2022-06-27\",\"endDate\":\"2022-07-26\",\"data\":\"to0Wwt.oSwt.Ktwtfftwt0Wbw.KdWw.K0.wtfoSwttdSwttKWwtttSw.SSSw.t.fw.tfWw.b.Ww.Wdbw.SfSw.W0.w..0Kw.0fSw.ffow.SdKw.oSow.o0ow.tWfw..fKw.0otw.fSbw.odKw.bfo\"},\"type\":\"day\"}],\"generalRatio\":[{\"word\":[{\"name\":\"php\",\"wordType\":1}],\"all\":{\"avg\":2362,\"yoy\":-8,\"qoq\":-1},\"pc\":{\"avg\":1534,\"yoy\":-9,\"qoq\":1},\"wise\":{\"avg\":827,\"yoy\":-6,\"qoq\":-5}},{\"word\":[{\"name\":\"java\",\"wordType\":1}],\"all\":{\"avg\":8904,\"yoy\":6,\"qoq\":3},\"pc\":{\"avg\":5123,\"yoy\":-7,\"qoq\":9},\"wise\":{\"avg\":3780,\"yoy\":34,\"qoq\":-2}}],\"uniqid\":\"82a7d95a2e0a153b17d3c49dde45a1c1\"},\"logid\":2717214935,\"message\":0}";
        $data = json_decode($jsonData, true);

        $baiduIndex = new BaiduIndex();
        $content = $baiduIndex->decryptData($data['data'], $ptbk);

        //etl
        $export = new Export();
        $csvName = $this->genCsvName("a.csv");
        $export->csv("storage/csv/" . $csvName, $content, true);
        //var_dump($content);
        exit;
    }

    /**
     * 生成名字
     *
     * @param bool $name
     * @return bool|string
     */
    public function genCsvName($name = false)
    {
        return $name ?? date("Y_m_d_H_i_s_", time()) . rand(1000, 9999) . ".csv";
    }
}
