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

    /**
     * 更新
     */
    public function update($params)
    {
        /*array_filter($params, function ($item) {

        });*/


    }

    /**
     * 登录
     *
     * @param array $params
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function login(array $params)
    {
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
    }

    /**
     * 注册
     *
     * @param array $params
     * @throws \Exception
     */
    public function register(array $params)
    {
        try {
            Db::beginTransaction();

            if (\App\Model\User::where('email', $params['email'])->exists()) {
                throw new \Exception("该邮箱已经被注册！");
            }
            /*if (\App\Model\User::where('username', $params['username'])->exists()) {
                throw new \Exception("该用户名已经被注册！");
            }*/

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

    /**
     * 退出
     *
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function logout()
    {
        $this->jwt->logout();
        return true;
    }
}