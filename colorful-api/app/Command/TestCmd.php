<?php

declare(strict_types=1);

namespace App\Command;

use App\Event\Demo;
use App\Service\RankService;
use Carbon\Carbon;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Context\Context;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Coroutine;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
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

    /**
     * @Inject()
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

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
