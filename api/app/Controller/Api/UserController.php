<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\ArticleService;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Taoran\HyperfPackage\Core\AbstractController;
use Taoran\HyperfPackage\Core\Code;

class UserController extends AbstractController
{

    /**
     * @Inject()
     * @var UserService
     */
    protected $userService;

    /**
     * 更新
     */
    public function update()
    {
        try {
            $params = $this->verify->requestParams([
                ['nickname', ''],
                ['sex', ''],
                ['headimg', ''],
                ['signature', ''],
                ['birthday', ''],
            ], $this->request);

            //参数验证
            $validator = $this->validationFactory->make(
                $params,
                [
                    'nickname' => 'max:20',
                    'sex' => 'in:0,1,2',
                    'signature' => 'max:255',
                ],
                [
                    'nickname.max' => '昵称不能大于20个字',
                    'sex.in' => '参数错误',
                    'signature' => '个性签名不能操作255个字',
                ]
            );

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $result = $this->userService->update($params);

            return $this->responseCore->success($result);
        } catch (\Exception $e) {
            return $this->responseCore->error(Code::VALIDATE_ERROR, $e->getMessage());
        }
    }

    /**
     * 重设密码
     */
    public function resetPassword()
    {

    }

    public function register()
    {
        try {
            $params = $this->verify->requestParams([
                ['email', ''],
                ['nickname', ''],
                ['password', ''],
                ['password_confirmation', ''],
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

            $this->userService->register($params);
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
        try {
            //获取参数
            $params = $this->verify->requestParams([
                ['email', ''],
                ['password', ''],
            ], $this->request);

            //参数验证
            $validator = $this->validationFactory->make(
                $params,
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ],
                [
                    'email.required' => '邮箱不能为空！',
                    'email.email' => '请填写正确的邮箱！',
                    'password.required' => '请填写密码！',
                ]
            );

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            //业务
            $result = $this->userService->login($params);
            //响应
            return $this->responseCore->success($result);
        } catch (\Exception $e) {
            return $this->responseCore->error(Code::VALIDATE_ERROR, $e->getMessage());
        }
    }

    /**
     * logout
     */
    public function logout()
    {
        $this->userService->logout();
        return $this->responseCore->success('操作成功');
    }
}
