<?php


namespace App\Service;

use Hyperf\Di\Annotation\Inject;

class ArticleTagService
{
    /**
     * @Inject()
     * @var \App\Model\ArticleTag
     */
    protected $articleTagModel;

    /**
     * 列表
     *
     * @param array $params
     * @return \Hyperf\Contract\LengthAwarePaginatorInterface
     */
    public function getList($params)
    {
        $list = $this->articleTagModel->getList(['id', 'name'], $params);
        return $list;
    }
}