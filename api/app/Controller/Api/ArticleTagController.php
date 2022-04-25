<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\ArticleTagService;
use Hyperf\Di\Annotation\Inject;
use Taoran\HyperfPackage\Core\AbstractController;

class ArticleTagController extends AbstractController
{

    /**
     * @Inject()
     * @var ArticleTagService
     */
    protected $articleTagService;

    public function index()
    {
        try {
            $params = $this->verify->requestParams([
                ['title', ''],
                ['is_all', 0],
                ['page_limit', 30],
            ], $this->request);

            $list =  $this->articleTagService->getList($params);
            return $this->responseCore->success($list);
        } catch (\Exception $e) {
            return $this->responseCore->error($e->getMessage());
        }
    }
}
