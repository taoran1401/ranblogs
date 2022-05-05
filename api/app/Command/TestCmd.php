<?php

declare(strict_types=1);

namespace App\Command;

use App\Event\Demo;
use App\Job\TestJob;
use App\Packages\DataInterface\src\BaiduIndex;
use App\Packages\DataInterface\src\Export;
use App\Service\RankService;
use Carbon\Carbon;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Context\Context;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Coroutine;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use function Taoran\HyperfPackage\Helpers\getDateByInterval;
use function Taoran\HyperfPackage\Helpers\mFristAndLast;
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

    /**
     * @Inject()
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     */
    protected $driver;

    public function __construct(ContainerInterface $container, DriverFactory $driverFactory)
    {
        $this->container = $container;
        $this->driver = $driverFactory->get('default');

        parent::__construct('test:cmd');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {
        //$res = $this->driver->push(new TestJob(), 0);
        return true;


        //关键词
//        $word = [
//            [
//                //组合词
//                [
//                    'name' => 'php',
//                    'wordType' => 1
//                ],
//                [
//                    'name' => 'golang',
//                    'wordType' => 1
//                ]
//            ],
//            [
//                //单个
//                [
//                    'name' => 'java',
//                    'wordType' => 1
//                ]
//            ]
//        ];
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
//            [
//                //单个
//                [
//                    'name' => 'java',
//                    'wordType' => 1
//                ]
//            ]
        ];
        //日期
        $startDate = '2011-01-01';
        $endDate = '2022-04-23';
        //地区
        $area = 0;

        $baiduIndex = new BaiduIndex();
        $cookie = 'PSTM=1619529893; BIDUPSID=A7911A2514EB4CC3EE1425BEEC0979CC; __yjs_duid=1_edf34bb9feb9f05b03d967cb836def6f1619967340911; H_WISE_SIDS=107318_110085_127969_153067_179345_184716_185634_189256_189755_191067_192206_192391_192408_193284_194085_194512_194520_195329_195342_195468_196049_196428_196514_196528_197242_197711_197782_197958_198120_198254_198929_199022_199082_199467_199490_199574_200736_200763_200958_201054_201107_201187_201233_201361_201541_201556_201576_201705_201978_201996_202058_202111_202565_202652_202760_202904_202910_203197_203250_203267_203542_203606_203629_204032_204099_204102_204112_204131_204154_204205_204262_204371_204432; MCITY=-289%3A; BAIDUID=0387E1D3A675D372665603DAD7A82B26:SL=0:NR=10:FG=1; BDORZ=B490B5EBF6F3CD402E515D22BCDA1598; BAIDUID_BFESS=0387E1D3A675D372665603DAD7A82B26:SL=0:NR=10:FG=1; BA_HECTOR=218l04aga4800la4q91h69vdo0r; uc_login_unique=1234358d7a251af63537ad425e136d03; uc_recom_mark=cmVjb21tYXJrXzM3MzAyOTQx; delPer=0; PSINO=6; BCLID=7732568761536106711; BDSFRCVID=A4kOJexroG0leprDjCbTMeKNyrpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF=tR30WJbHMTrDHJTg5DTjhPrMLHOWWMT-MTryKKJs54JKsbcJ-lOYbfPN2q5jLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-D8wj6-h3e; BCLID_BFESS=7732568761536106711; BDSFRCVID_BFESS=A4kOJexroG0leprDjCbTMeKNyrpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF_BFESS=tR30WJbHMTrDHJTg5DTjhPrMLHOWWMT-MTryKKJs54JKsbcJ-lOYbfPN2q5jLbvkJGnRh4oNBUJtjJjYhfO45DuZyxomtfQxtNRJQKDE5p5hKq5S5-OobUPUDUJ9LUkJ3gcdot5yBbc8eIna5hjkbfJBQttjQn3hfIkj2CKLK-oj-D8wj6-h3e; Hm_lvt_d101ea4d2a5c67dab98251f0b5de24dc=1649218125,1649324141,1649389757,1650854518; BDUSS=NDZ2FyT3lmdktRcEdhTDMta1JnZk0zSUZXdTI1azg2UHJMdVVRM2dEWU5tSTFpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA0LZmINC2ZiOF; BDUSS_BFESS=NDZ2FyT3lmdktRcEdhTDMta1JnZk0zSUZXdTI1azg2UHJMdVVRM2dEWU5tSTFpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA0LZmINC2ZiOF; CHKFORREG=27eec5721079530a30ebcd4cc39faa47; Hm_up_d101ea4d2a5c67dab98251f0b5de24dc=%7B%22uid_%22%3A%7B%22value%22%3A%221433106078%22%2C%22scope%22%3A1%7D%7D; bdindexid=fk6jlrhvai9488sqpeem62g874; Hm_lpvt_d101ea4d2a5c67dab98251f0b5de24dc=1650854947; ab_sr=1.0.1_NzY4NDVkMjhlNWQwNWJhMDcwZjE3MzViZjgyMzQwY2Q3NDMxYTgzNTgwNTk1YTdmMDkxODAyOTRlYzViNDNkM2FjZjI3NzM1ZjMwOGM4NGU4YTM0ODU5ZmVmMzNkYzcyNTVjOTRhNGEwZThkYjdkODZkMjJjZThhNjJmODgxNjJjYzQxOWI5YjMzMzFiOWU4MWUyYWUzYTE5Zjk3NDAyNQ==; __yjs_st=2_MzIwN2U3ZWJlY2Y4MjI4NzU4ZjI0ZjkxNGNjOWJkZjg5YThmN2IzNzc2YTFiNjRkNzRiOTIxZmU3ZDdkNGI3ODM1ZGViNTFjMjQyZmZiMWI5MDhmMDRhYmNhYWRlMjY3OTgzN2E4OGNlMWYxMGViNzhiNTRiOTdiYzQ1MTM0OGQ3OWI0MjhjYWE2MmNiMDVjYTY3NGNmYmQzMDNkNzA3YjgwYzJkNTRlMzI2NzhhMzI1OWJiYmM5ZjM5ZjBlYmEwXzdfZmNjNGY3NWM=; H_PS_PSSID=36309_31660_35912_36166_34584_36121_36342_36075_36297_36344_26350_36349_36061; RT="z=1&dm=baidu.com&si=rnjh6fung9b&ss=l2e45lqq&sl=i&tt=cav&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&ld=luuk"';

        //mFristAndLast
        $yearScope = getDateByInterval($startDate, $endDate, 'year');
        foreach ($yearScope as $key => $val) {
            if ($key > 0) {
                $isHeader = false;
            }
            $res = $baiduIndex->setCookie($cookie)
                ->search($word, $val['startDate'], $val['endDate'], $area);

            $export = new Export();
            $export->csv('test.csv', $res, ($key > 0) ? false : true);

            sleep(3);
        }

        return true;

        var_dump($res);
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

    /**
     * 发送邮件
     * @param $address
     * @param $subject
     * @param $message
     * @return array
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function send_email($address,$message) {
        $mail=new PHPMailer(true);
        //开启调试
        //$mail->SMTPDebug = 1;
        // 设置PHPMailer使用SMTP服务器发送Email
        $mail->IsSMTP();
        $mail->IsHTML(true);
        // 设置邮件的字符编码，若不指定，则为'UTF-8'
        $mail->CharSet='UTF-8';
        // 添加收件人地址，可以多次使用来添加多个收件人
        $mail->AddAddress($address);
        // 设置邮件正文
        $mail->Body=sys_config(system_config::EMAIL_MESSAGE)."\n".$message??""."\n".$message;
        // 设置邮件头的From字段。
        $mail->From=sys_config(system_config::EMAIL_FROM);                          //from头，和邮箱地址一致
        // 设置发件人名字
        $mail->FromName=sys_config(system_config::EAMIL_SEND_NAME);
        // 设置邮件标题
        $mail->Subject=sys_config(system_config::EMAIL_SUBJECT);
        // 设置SMTP服务器。
        $mail->Host=sys_config(system_config::EMAIL_SMTP);                         //SMTP服务器
        //设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';
        // 设置SMTP服务器端口。
        $port=sys_config(system_config::EMAIL_PORT);
        $mail->Port=empty($port)?"465":$port;
        // 设置为"需要验证"
        $mail->SMTPAuth=true;
        // 设置用户名和密码。
        $mail->Username=sys_config(system_config::EMAIL_USERNAME);                  //用户名
        $mail->Password=sys_config(system_config::EMAIL_PASSWORD);                 //邮件授权码
        // 发送邮件。
        if(!$mail->Send()) {
            $mailerror=$mail->ErrorInfo;
            return array("error"=>1,"message"=>$mailerror);
        }else{
            return array("error"=>0,"message"=>"success");
        }
    }
}
