<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\ArticleService;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Taoran\HyperfPackage\Core\AbstractController;

class UserController extends AbstractController
{

    /**
     * @Inject()
     * @var UserService
     */
    protected $userService;

    public function register()
    {
        try {
            /*$params = $this->verify->requestParams([
                ['account', ''],
                ['password', ''],
            ], $this->request);

            //参数验证
            $validator = $this->validationFactory->make(
                $params,
                [
                    'email' => 'required|email',
                    'nickname' => 'required',
                    'password' => 'required|min:8|max:16|confirmed',
                    'password_confirmation' => 'required',
                ],
                [
                    'email.required' => '邮箱不能为空！',
                    'email.email' => '请填写正确的邮箱！',
                    'nickname.required' => '请填写昵称！',
                    'password.required' => '请填写密码！',
                    'password.min' => '密码应该在8-16位！',
                    'password.max' => '密码应该在8-16位！',
                    'password.confirmed' => '两次密码不一致！',
                    'password_confirmation.required' => '请输入重复密码！',
                ]
            );

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $this->userService->register($params);*/
            return $this->responseCore->success('注册成功！');
        } catch (\Exception $e) {
            return $this->responseCore->error(Code::VALIDATE_ERROR, $e->getMessage());
        }

    }

    /**
     * 登录
     */
    public function login()
    {
        //邮箱，微信公众号
    }

    /**
     * logout
     */
    public function logout()
    {
        $this->jwt->logout();
        return true;
    }
}
