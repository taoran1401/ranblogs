<?php


namespace App\Service;


use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;

use Hyperf\Validation\Rule;
use Phper666\JwtAuth\Jwt;
use Taoran\HyperfPackage\Core\AbstractController;
use function Taoran\HyperfPackage\Helpers\Password\create_password;
use function Taoran\HyperfPackage\Helpers\Password\eq_password;
use function Taoran\HyperfPackage\Helpers\set_save_data;


class UserService extends AbstractController
{
    /**
     * @Inject()
     * @var Jwt
     */
    protected $jwt;

    public function login(array $params)
    {
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

        try {
            $user = \App\Model\User::where('email', $params['email'])->first();
            if (!$user) {
                throw new \Exception("用户不存在！");
            }

            //密码check
            if (!eq_password($user->password, $params['password'], $user->salt)) {
                throw new \Exception("密码错误！");
            }

            //创建token
            $token = (string) $this->jwt->getToken(['user_id' => $user->id]);
            return ['token' => $token, 'nickname' => $user->nickname,'expires' => $this->jwt->getTTL()];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function register(array $params)
    {

        try {
            Db::beginTransaction();

            if (\App\Model\User::where('email', $params['email'])->exists()) {
                throw new \Exception("该邮箱已经被注册！");
            }

            if (\App\Model\User::where('nickname', $params['nickname'])->exists()) {
                throw new \Exception("该昵称已经被使用！");
            }

            //创建密码
            $password = create_password($params['password'], $salt);
            $user = new \App\Model\User();
            set_save_data($user, [
                'email' => $params['email'],
                'nickname' => $params['nickname'],
                'password' => $password,
                'salt' => $salt,
            ]);
            $user->save();

            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}