<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use App\Model\Article;
use App\Packages\Crawler\src\Crawler;
use Hyperf\Di\Annotation\Inject;
use Marquine\Etl\Etl;
use function Taoran\HyperfPackage\Helpers\get_msectime;

class IndexController extends AbstractController
{
    /**
     * @Inject()
     * @var Article
     */
    protected $model;

    public function index()
    {
        $arr = [
            'status' => 200,
            'data' => 'ok'
        ];
        return json_encode($arr);
    }


    /**
     * 获取基础url
     *
     * @param $url
     * @return string
     */
    public function getBaseUrl($url)
    {
        $parse_url = parse_url($url);
        $base_url = $parse_url['scheme'] . '://' . $parse_url['host'];
        if (!empty($parse_url['port'])) {
            $base_url .= ':' . $parse_url['port'];
        }
        return $base_url;
    }

    /**
     * 获取基础信息
     *
     * @param $url
     * @param $body
     * @return array
     */
    public function extractBaseInfo($url, $body)
    {
        $extract_content = [];
        //icon
        $extract_content['icon'] = $url . '/favicon.ico';
        //title
        $reg_title='/<title>(.*?)<\/title>/is';
        preg_match_all($reg_title, $body, $matches_title);
        $extract_content['title_ful'] = $matches_title[1][0] ?? '';
        $extract_content['title'] = trim(explode('-', $matches_title[1][0])[0]);
        //description
        $reg_description='/<meta name="description" .*?content="(.*?)".*?>/is';
        preg_match_all($reg_description, $body, $matches);
        $extract_content['description'] = $matches[1][0] ?? '';
        return $extract_content;
    }

    /**
     * 提取url
     */
    public function extractUrl($data)
    {
        $reg='/<a .*?href="(.*?)".*?>/is';
        preg_match_all($reg, $data, $matches);
        $matches = array_filter($matches[1], function ($val) {
            var_dump($val);
            $url_reg = '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i';
            preg_match($url_reg, $val, $matches_url);
            /*if ($val != '#' && $val != '' && strpos($val, '#') != 0) {
                return $val;
            }*/
            var_dump($matches_url);
        });
        return $matches;
    }

    /**
     * 快照
     *
     * @param $filename
     * @param $data
     * @return bool
     */
    public function snapshot($filename, $data)
    {
        $filename = md5($filename) . get_msectime() . '.html';
        $snapshot_path = BASE_PATH . '/public/snapshot/' . $filename;
        file_put_contents($snapshot_path, $data);
        return true;
    }


    public function plan()
    {
        // data: dm month
        // data: y,s
        //
    }
}
