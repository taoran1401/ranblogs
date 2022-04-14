<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\ArticleService;
use Hyperf\Di\Annotation\Inject;
use Taoran\HyperfPackage\Core\AbstractController;

class CollectController extends AbstractController
{

    /**
     * @Inject()
     * @var ArticleService
     */
    protected $articleService;

    public function index()
    {
        try {
            $params = $this->verify->requestParams([
                ['title', ''],
                ['is_all', 0],
                ['tag_id', ''],
                ['page', 1],
                ['page_limit', 20],
            ], $this->request);
            //只获取可显示的
            $params['is_show'] = 1;
            $list =  $this->articleService->getList($params);
            return $this->responseCore->success($list);
        } catch (\Exception $e) {
            return $this->responseCore->error($e->getMessage());
        }
    }

    public function show($id)
    {
        $data =  $this->articleService->getOne((int)$id);
        return $this->responseCore->success($data);
    }

    /**
     * 归档
     *
     * @return array
     */
    public function Archive()
    {
        $list = $this->articleService->Archive();
        return $this->responseCore->success($list);
    }
}
