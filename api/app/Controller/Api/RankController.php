<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\ConfigService;
use Hyperf\Di\Annotation\Inject;
use Taoran\HyperfPackage\Core\AbstractController;

class RankController extends AbstractController
{
    /**
     *
     * @Inject
     * @var \App\Service\RankService
     */
    protected $rankService;

    public function baiduIndexInsert()
    {
        try {
            $list = $this->rankService->baiduIndexInsert($this->request->all());
            return $this->responseCore->success('ok');
        } catch (\Exception $e) {
            return $this->responseCore->error($e->getMessage());
        }
    }
}