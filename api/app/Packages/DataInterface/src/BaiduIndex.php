<?php

namespace App\Packages\DataInterface\src;

use App\Packages\DataInterface\src\Util;

class BaiduIndex
{
    /** @var string 搜索指数 */
    const SEARCH_URL = 'https://index.baidu.com/api/SearchApi/index?';

    /** @var string  */
    const LIVE_URL = 'https://index.baidu.com/api/LiveApi/getLive?';

    /** @var string  资讯 */
    const NEWS_URL = 'https://index.baidu.com/api/NewsApi/getNewsIndex?';

    /** @var string  资讯指数概览 */
    const FEED_URL = 'https://index.baidu.com/api/FeedSearchApi/getFeedIndex?';

    /** @var string  */
    const PTBK_URL = 'https://index.baidu.com/Interface/api/ptbk?uniqid=';

    /** @var cookie */
    protected $cookie;

    /**
     * build search url
     *
     * @param $word
     * @param $startDate
     * @param $endDate
     * @param $area
     * @return string
     */
    public function buildSearchUrl($word, $startDate, $endDate, $area)
    {
        $params = [
            'word' => urlencode(json_encode($word)),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'area' => $area,
        ];
        $uri = '';
        foreach ($params as $key => $val) {
            $uri .= $key . '=' . $val . '&';
        }
        $uri = rtrim($uri, '&');
        return self::SEARCH_URL . $uri;
    }

    /**
     * set cookie
     *
     * @param $cookie
     * @return $this
     */
    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
        return $this;
    }

    /**
     * search
     *
     * @param $word
     * @param $startDate
     * @param $endDate
     * @param $area
     * @return mixed
     * @throws \Exception
     */
    public function search($word, $startDate, $endDate, $area, $export = '')
    {
        //构建url
        $searchUrl = $this->buildSearchUrl($word, $startDate, $endDate, $area);

        $option = [
            'cookie' => $this->cookie
        ];
        $data = json_decode(Util::httpRequest($searchUrl, 'get', false, $option), true);
        if ($data['status'] != 0) {
            throw new \Exception($data['message']);
        }

        //数据解析
        $data = $this->decryptData($data['data']);
        return $data;
    }

    /**
     * key获取，用于解密search获取的data
     *
     * @param $uniqid
     * @return mixed
     * @throws \Exception
     */
    public function getKey($uniqid)
    {
        $url = self::PTBK_URL . $uniqid;
        $result = json_decode(Util::httpRequest($url, 'get', false, [
            'cookie' => $this->cookie
        ]), true);

        if (!(isset($result['status']) && $result['status'] == 0)) {
            throw new \Exception('key获取失败！');
        }
        return $result['data'];
    }

    /**
     * decrypt data
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function decryptData($data)
    {
        $uniqid = $data['uniqid'];
        $dataKey = $this->getKey($uniqid);
        $userIndexes = $data['userIndexes'];
        foreach ($userIndexes as $key => $val) {
            // words
            $word = $this->implodeWords($val['word']);
            // decrypt
            $userIndexes[$key]['all']['data'] = $this->decrypt($dataKey, $val['all']['data']);
            // format data
            $dataScope = \App\Packages\DataInterface\src\Util::getDateByInterval($val['all']['startDate'], $val['all']['endDate'], 'day');
            foreach ($dataScope as $_key => $date) {
                $userIndexes[$key]['all']['formatData'][] = [
                    'word' => $word,
                    'date' => $date,
                    'value' => $userIndexes[$key]['all']['data'][$_key] ?? 0,
                ];
            }
        }
        return $userIndexes;
    }

    /**
     * 关键词组合
     *
     * @param $words
     * @return string
     */
    public function implodeWords($words)
    {
        $word = '';
        foreach ($words as $_word) {
            $word .= $_word['name'] . '/';
        }
        return trim($word, '/');
    }

    /**
     * 内容解密
     *
     * @param $key
     * @param $data
     * @return array
     */
    public function decrypt($key, $data)
    {
        $arr = [];
        $content = [];
        $keyLen = strlen($key);
        $dataLen = strlen($data);
        for ($i = 0; $i < floor($keyLen / 2); $i++) {
            @$arr[$key[$i]] = $key[floor($keyLen / 2) + $i];
        }
        for ($j = 0; $j < $dataLen; $j++) {
            array_push($content, $arr[$data[$j]]);
        }
        $content = explode(',', implode('', $content));
        return $content;
    }
}