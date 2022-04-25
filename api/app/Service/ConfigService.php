<?php


namespace App\Service;

use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Taoran\HyperfPackage\Core\AbstractController;

class ConfigService extends AbstractController
{
    /**
     * @Inject()
     * @var \App\Model\Config
     */
    protected $configModel;

    public function getData($params)
    {
        $list = $this->configModel->getList(['id', 'code', 'desc', 'value'], $params, function ($query) use ($params) {
            $query->orderBy('id', 'ASC');
            if (isset($params['codes']) && $params['codes'] != '') {
                $query->whereIn('code', $params['codes']);
            }
        });

        if (!empty($list)) {
            $list->each(function ($item) {
                $item->value = htmlspecialchars_decode($item->value);
            });
            $list = $list->toArray();
            $list = array_combine( array_column($list, 'code'), array_column($list, 'value'));
        }

        return $list;
    }

    /**
     * 根据code获取config
     *
     * @param $code
     * @return \Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|object|null
     */
    public function getConfigByCode($code)
    {
        $data = $this->configModel->getOne(['code', 'desc', 'value'], function ($query) use ($code) {
            $query->where('code', $code);
        });

        return $data;
    }
}