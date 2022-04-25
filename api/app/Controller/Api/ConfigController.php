<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\ConfigService;
use Hyperf\Di\Annotation\Inject;
use Taoran\HyperfPackage\Core\AbstractController;

class ConfigController extends AbstractController
{

    /**
     * @Inject()
     * @var ConfigService
     */
    protected $configService;

    public function index()
    {
        try {
            $list = $this->configService->getData([
                'codes' => [
                    'about_us',
                    'base_setting_name',
                    'base_setting_favicon',
                    'base_setting_keyword',
                    'base_setting_description',
                ]
            ]);
            return $this->responseCore->success($list);
        } catch (\Exception $e) {
            return $this->responseCore->error($e->getMessage());
        }
    }

    /**
     * about_us
     *
     * @return array
     */
    public function aboutUs()
    {
        $data = $this->configService->getConfigByCode('about_us');
        return $this->responseCore->success($data);
    }
}
