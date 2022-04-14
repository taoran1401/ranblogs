<?php


namespace App\Service;

use Hyperf\Di\Annotation\Inject;

class CollectTagService
{
    /**
     * @Inject()
     * @var \App\Model\AollectTag
     */
    protected $collectTagModel;

    /**
     * 列表
     *
     * @param array $params
     * @return \Hyperf\Contract\LengthAwarePaginatorInterface
     */
    public function getList($params)
    {
        $list = $this->collectTagModel->getList(['id', 'name'], $params);
        return $list;
    }
}