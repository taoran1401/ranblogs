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
use function Taoran\HyperfPackage\Helpers\set_save_data;

/**
 * @Command
 */
#[Command]
class SendEmail extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('send:email');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('发送邮件');
        $this->addOption('type', 'type', InputOption::VALUE_REQUIRED, '发送邮件', 'code');
        $this->addOption('addressee', 'addressee', InputOption::VALUE_REQUIRED, '收件人');
    }

    public function handle()
    {
        $type = $this->input->getOption('type');
        //收件人
        $addressee = $this->input->getOption('addressee');

        switch ($type) {
            case 'code':
                //发送验证码
                $subject = 'colorful的验证码';
                $code = rand(100000, 999999);
                $body = '<html><head>
<meta http-equiv="Content-Language" content="zh-cn">
<meta http-equiv="Content-Type" content="text/html; charset=GB2312"></head>
<body>
欢迎来到 colorful 感谢您的使用！ <br />
这是您的验证码： ' . $code . '
<br />
</body>
</html>';
                $this->send($addressee, $subject, $body);
                break;
            case 'link':
                //重设密码链接
                $subject = 'colorful的验证码';
                $link = 'xxxxxxx';
                $body = '<html><head>
<meta http-equiv="Content-Language" content="zh-cn">
<meta http-equiv="Content-Type" content="text/html; charset=GB2312"></head>
<body>
欢迎来到 colorful 感谢您的使用！ <br />
请通过该链接进行操作： ' . $link . '
<br />
</body>
</html>';
                $this->send($addressee, $subject, $body);
                break;
        }
    }

    /**
     * 验证码
     *
     * @param $addressee
     * @param $subject
     * @param $body
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send($addressee, $subject, $body)
    {
        $mail = $this->mail();
        $mail->FromName = 'colorful';       //发件人名称
        $mail->AddAddress($addressee);      //收件人
        $mail->Subject = $subject;          //邮件主题
        $mail->Body = $body;
        $mail->IsHTML(true);
        $mail->Send();
        return true;
    }
}
