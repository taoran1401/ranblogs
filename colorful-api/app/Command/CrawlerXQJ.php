<?php

declare(strict_types=1);

namespace App\Command;


use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Di\Annotation\Inject;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Psr\Container\ContainerInterface;
use function Taoran\HyperfPackage\Helpers\get_msectime;
use function Taoran\HyperfPackage\Helpers\set_save_data;

/**
 * @Command
 */
#[Command]
class CrawlerXQJ extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('crawler:xqj');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {
        //发送邮件
        $this->sendEmail();
        exit;
        //获取net基础信息
        $this->extractBaseInfoSave('https://www.coolzhanweb.com/');

        $dict = new \Taoran\FilterWord\Dict();
        //两个参数： 路径和文件
        $dict->setDictFile(BASE_PATH . '/public/dict/', 'dict.php');
        // 添加词汇
        /*$addResult = $dict->add(['词汇一', '词汇二', '词汇五']);
        // 删除词汇
        $delResult = $dict->destroy(['词汇一']);
        // 获取词库内容
        $dictContent = $dict->getDictContent();
        // 获取词库节点树
        $dictDFAContent = $dict->getDictDFAContent();

        //$addResult = $dict->reset(['词汇一', '词汇二']);
        var_dump($delResult);
        exit;*/
        $text = "数据的垦利街道王八，监考老师地方非法web的非第三词汇二方王八蛋数据库弗拉水电费！！";
        $filter = new \Taoran\FilterWord\Filter();
        $filter->dict->setDictFile(BASE_PATH . '/public/dict/', 'dict.php');
        $result = $filter->filter($text);
        var_dump($result);
        exit;
    }

    public function sendEmail()
    {
        //qq邮箱： 1401..
        //kqahhzplowylbaae
        try {
            $mail = new PHPMailer();
            /*$mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];*/
            $mail->SMTPDebug = 1;
            $mail->IsSMTP();
            $mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
            $mail->SMTPAuth = true; //开启认证
            $mail->SMTPSecure = 'ssl'; // 使用安全协议
            $mail->Port = 465; //端口请保持默认

//            $mail->Host = "smtp.163.com"; //smtp服务器
//            $mail->Username = "taoran0796@163.com"; //这个可以替换成自己的邮箱
//            $mail->Password = "SAGQTBWQQDHWQPTS"; //注意 这里是写smtp的授权码 写的不是QQ密码，此授权码不可用
//            $mail->From = "taoran0796@163.com"; //发件人邮箱

            $mail->Host = "smtp.qq.com"; //smtp服务器
            $mail->Username = "1401696973@qq.com"; //这个可以替换成自己的邮箱
            $mail->Password = "mpxtaaqxxktoicbb"; //注意 这里是写smtp的授权码 写的不是QQ密码，此授权码不可用
            $mail->From = "1401696973@qq.com"; //发件人邮箱
            //mpxtaaqxxktoicbb
            //$mail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could not execute: /var/qmail/bin/sendmail ”的错误提示
            //$mail->AddReplyTo("taoran0796@163.com","mckee");//回复地址

            $mail->FromName = 'colorful test';     //发件人名称
            $mail->AddAddress('2357144431@qq.com'); //收件人
            $mail->Subject = 'colorful的邮箱验证'; //邮件主题
            $mail->Body = '<html><head>
<meta http-equiv="Content-Language" content="zh-cn">
<meta http-equiv="Content-Type" content="text/html; charset=GB2312"></head>
<body>
欢迎来到 <br /><br />
感谢您注册为本站会员！<br /><br />
</body>
</html>';  //正文
            //$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略
//            $mail->WordWrap = 80; // 设置每行字符串的长度
//            $mail->AddAttachment("f:/test.png"); //可以添加附件
            $mail->IsHTML(true);
            $mail->Send();

        } catch (phpmailerException $e) {
            var_dump("邮件发送失败：".$e->errorMessage());
        }
        return true;
    }

    /**
     * 提取基本信息入库
     *
     * @param $url
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function extractBaseInfoSave($url)
    {
        if (!$this->isUrl($url)) {
            throw new \Exception("url无效！");
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);
        $body = (string)$response->getBody();
        $info = $this->extractBaseInfo($url, $body);

        $collect = \App\Model\Collect::where('is_on', 1)->where('url', $url)->first();
        if ($collect) {
            throw new \Exception("请不要重复收录！");
        }

        $collectModel = new \App\Model\Collect();
        set_save_data($collectModel, [
            'url' => $url,
            'icon' => $info['icon'],
            'title' => $info['title'],
            'keywords' => $info['keywords'],
            'description' => $info['description'],
            'created_at' => get_msectime(),
            'updated_at' => get_msectime(),
        ]);
        $collectModel->save();
    }

    /**
     * 获取基础url
     *
     * @param $url
     * @return string
     */
    public function getBaseUrl($url)
    {
        $parse_url = parse_url($url);
        $base_url = $parse_url['scheme'] . '://' . $parse_url['host'];
        if (!empty($parse_url['port'])) {
            $base_url .= ':' . $parse_url['port'];
        }
        return $base_url;
    }

    /**
     * 获取基础信息
     *
     * @param $url
     * @param $body
     * @return array
     */
    public function extractBaseInfo($url, $body)
    {
        $extract_content = [];
        //icon
        $extract_content['icon'] = $url . '/favicon.ico';
        //title
        $reg_title='/<title>(.*?)<\/title>/is';
        preg_match_all($reg_title, $body, $matches_title);
        $extract_content['title'] = $matches_title[1][0] ?? '';
        //$extract_content['title'] = trim(explode('-', $matches_title[1][0])[0]);
        //keyword
        $reg_keywords='/<meta name="keywords" .*?content="(.*?)".*?>/is';
        preg_match_all($reg_keywords, $body, $matches);
        $extract_content['keywords'] = $matches[1][0] ?? '';
        //description
        $reg_description='/<meta name="description" .*?content="(.*?)".*?>/is';
        preg_match_all($reg_description, $body, $matches);
        $extract_content['description'] = $matches[1][0] ?? '';
        return $extract_content;
    }

    /**
     * 提取url
     */
    public function extractUrl($data)
    {
        $reg='/<a .*?href="(.*?)".*?>/is';
        preg_match_all($reg, $data, $matches);
        $matches = array_filter($matches[1], function ($val) {
            var_dump($val);
            $url_reg = '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i';
            preg_match($url_reg, $val, $matches_url);
            /*if ($val != '#' && $val != '' && strpos($val, '#') != 0) {
                return $val;
            }*/
            var_dump($matches_url);
        });
        return $matches;
    }

    /**
     * 快照
     *
     * @param $filename
     * @param $data
     * @return bool
     */
    public function snapshot($filename, $data)
    {
        $filename = md5($filename) . get_msectime() . '.html';
        $snapshot_path = BASE_PATH . '/public/snapshot/' . $filename;
        file_put_contents($snapshot_path, $data);
        return true;
    }

    /**
     * 验证是否url
     *
     * @param $url
     * @return bool
     */
    public function isUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        return true;
    }
}
