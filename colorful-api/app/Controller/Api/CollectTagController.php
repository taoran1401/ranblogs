<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\CollectTagService;
use Hyperf\Di\Annotation\Inject;
use Taoran\HyperfPackage\Core\AbstractController;

class CollectTagController extends AbstractController
{

    /**
     * @Inject()
     * @var CollectTagService
     */
    protected $collectTagService;

    public function index()
    {
        try {
            $params = $this->verify->requestParams([
                ['title', ''],
                ['is_all', 0],
                ['page_limit', 30],
            ], $this->request);

            $list =  $this->collectTagService->getList($params);
            return $this->responseCore->success($list);
        } catch (\Exception $e) {
            return $this->responseCore->error($e->getMessage());
        }
    }
}
