<?php

namespace App\Packages\DataInterface\src;

use App\Packages\DataInterface\src\Util;

class BiaduIndex
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


    /**
     * 装载到mysql
     *
     * @param $content
     * @param $model
     */
    public function loadMysql($content, $model)
    {
        $content = $content['data']['userIndexes'];
        foreach ($content as $key => $val) {
            foreach ($val['all']['formatdata'] as $key => $_val) {
                $insert = [
                    'name' => $val['word'][0]['name'],
                    'other' => 'tmp',
                    'origin' => 1,//来源：1：百度指数
                    'type' => 0,//0：动漫角； 1：动漫角色
                    'date' => $_val['date'],
                    'value' => $_val['value'],
                ];

                $model->updateOrInsert(
                    ['date' => $insert['date'], 'name' => $insert['name'], 'origin' => $insert['origin'], 'type' => $insert['type']],
                    $insert
                );
            }
        }
    }

    public function search($params)
    {
        $url = self::SEARCH_URL . Util::buildGetParams($params);
        $option = [
            'cookie' => 'BIDUPSID=DB2E1EE7D6DB6C2F0D8208A6ED4B25E5; PSTM=1634463614; __yjs_duid=1_e14917d477c6fd337756e23ea9d63eac1634484789836; BDUSS=G1rNzJPTDBqYVNKekh-TXJiWk0yM0xCZkNlc1dZejF-Wm1rWVBIRS1jUnBYMVJpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAGnSLGJp0ixiM; BDUSS_BFESS=G1rNzJPTDBqYVNKekh-TXJiWk0yM0xCZkNlc1dZejF-Wm1rWVBIRS1jUnBYMVJpSVFBQUFBJCQAAAAAAAAAAAEAAACedmtVx9bP~rih0~DO9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAGnSLGJp0ixiM; BDORZ=B490B5EBF6F3CD402E515D22BCDA1598; Hm_up_d101ea4d2a5c67dab98251f0b5de24dc=%7B%22uid_%22%3A%7B%22value%22%3A%221433106078%22%2C%22scope%22%3A1%7D%7D; MCITY=-257%3A; BAIDUID=6436371E5C3CAFD0165327E32CF5746E:SL=0:NR=10:FG=1; Hm_lvt_d101ea4d2a5c67dab98251f0b5de24dc=1649156763,1649248789,1649333616,1649484708; bdindexid=jd9jp8k6916qtbf62ttp503657; BDSFRCVID=qauOJexroG0xWuJD_f-CMeXktQpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF=tbk8oKP2fC03HRjvMn__DT5QqxbXq-o822OZ0l8KtfcISIoHMxOpKR-uXfovab0HJgOyhIOmWIQHDPQaeM7tQRKv0loPQlkLBJ74KKJxttPWeIJo5fcI-TLOhUJiB5JMBan7_pbIXKohJh7FM4tW3J0ZyxomtfQxtNRJ0DnjtnLhbCDrMtTfbJvH-xQ0KnLXKKOLVKK53q7ketn4hUt22h-0y4O4bJ5TBCTvB-ol-U3WoD52QhrKQfArbxrKa4rRQjIL-qcH0KQpsIJM5b-aqt0k5ec4Bn3baKviaKOjBMb1MhbDBT5h2M4qMxtOLR3pWDTm_q5TtUJMeCnTDMFhe6oMMfTbKRkXKD600PK8Kb7VbpkGMUnkbJkXhPtjhpbtWen4L-02BR5ijPbhKhJWWjL7QbrH0x7T5D5KBR6gQtKVSlcNLTjpQT8r5h-De434b4j4aPbeab3vOIOTXpO1jM0zBN5thURB2DkO-4bCWJ5TMl5jDh3Mb6ksD-Ftqj_s2Cob04P8KJjEe-Kk-PnVen__5tnZKRvHa2kjXU3aWqbre66JXtoiMn0uKbJOX6Qn3N5HKlRx5JTHjJ6e3R_V3xI8LNj405OTbTADsRbNb66pO-bghPJvyTtsXnO7tU3lXbrtXp7_2J0WStbKy4oTjxL1Db3JKjvMtgDtVJO-KKChMC-GDfK; BDSFRCVID_BFESS=qauOJexroG0xWuJD_f-CMeXktQpWxY5TDYrELPfiaimDVu-VJeC6EG0Pts1-dEu-EHtdogKK3gOTH4DF_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF_BFESS=tbk8oKP2fC03HRjvMn__DT5QqxbXq-o822OZ0l8KtfcISIoHMxOpKR-uXfovab0HJgOyhIOmWIQHDPQaeM7tQRKv0loPQlkLBJ74KKJxttPWeIJo5fcI-TLOhUJiB5JMBan7_pbIXKohJh7FM4tW3J0ZyxomtfQxtNRJ0DnjtnLhbCDrMtTfbJvH-xQ0KnLXKKOLVKK53q7ketn4hUt22h-0y4O4bJ5TBCTvB-ol-U3WoD52QhrKQfArbxrKa4rRQjIL-qcH0KQpsIJM5b-aqt0k5ec4Bn3baKviaKOjBMb1MhbDBT5h2M4qMxtOLR3pWDTm_q5TtUJMeCnTDMFhe6oMMfTbKRkXKD600PK8Kb7VbpkGMUnkbJkXhPtjhpbtWen4L-02BR5ijPbhKhJWWjL7QbrH0x7T5D5KBR6gQtKVSlcNLTjpQT8r5h-De434b4j4aPbeab3vOIOTXpO1jM0zBN5thURB2DkO-4bCWJ5TMl5jDh3Mb6ksD-Ftqj_s2Cob04P8KJjEe-Kk-PnVen__5tnZKRvHa2kjXU3aWqbre66JXtoiMn0uKbJOX6Qn3N5HKlRx5JTHjJ6e3R_V3xI8LNj405OTbTADsRbNb66pO-bghPJvyTtsXnO7tU3lXbrtXp7_2J0WStbKy4oTjxL1Db3JKjvMtgDtVJO-KKChMC-GDfK; BDRCVFR[feWj1Vr5u3D]=I67x6TjHwwYf0; delPer=0; PSINO=6; H_PS_PSSID=35835_31254_34813_36088_36166_34584_36140_36121_36075_36126_36109_36258_26350_36115_36091_36061; Hm_lpvt_d101ea4d2a5c67dab98251f0b5de24dc=1649511885; ab_sr=1.0.1_MTQxZjE1OTA5ZWI5ZjdiMjRmYWI1ZDU5YzMxZjg0YmIwZTE2NzFlMzRlZTYyMTIxM2RlZWYwZTA4YWM3NDY1NTQ3YTRkZWI0YWQ5MGFkMWQ2ZDZiNjAxN2ZjMDRhNWVmMzYwMTA3YjhjMmVhY2VmMDY5YTdlYTYxNTUxMTlkZTIwYTBlMzBhMDVkOTQ1ZTM5OWViNDliMzcyZTVkOGJlZA==; __yjs_st=2_OTg5ZmM1Yjc3ODIwMTlmODkwOThkNGE4ODY1OTRmNmVkMTg1NTM0NzYyZjNlY2EwOWJlOGExNGRhZjU0OWNiYTZkYmJlNmRjNTg3NThkNDhjNGE5NzBjNDQ2NjU0ZmE3NjUwNjhmODRmMzZmN2UxYmM4NmJhZDRkNTBhZWVlNTY2NjkyMDA1YjU0ZTVlYzRlZTU4ODUxMmViY2U0ODRhOGU4M2UwNzRmOTlkYjZmOTA2NjA4MDJkMWE1MDllMDY0XzdfYzZlNjEwNTU=; RT="sl=f&ss=l1rvcz34&tt=a1u&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&z=1&dm=baidu.com&si=ulnnza0nq18&ld=1fhav"; BA_HECTOR=0ha1ak8020a40g00bq1h533gc0q'
        ];
        $data = json_decode(Util::httpRequest($url, 'get', false, $option), true);
        if ($data['status'] != 0) {
            throw new \Exception($data['message']);
        }

        //TODO: 返回后数据处理
        /*foreach ($data['data'] as $key => $val) {

        }*/
    }

    public function getKey($uniqid)
    {
        $url = self::PTBK_URL . $uniqid;
        return Util::httpRequest($url);
    }

    /**
     * search内容解密
     *
     * @param $key
     * @param $data
     */
    public function decrypt($key, $data)
    {
/*        let a = key
        let i = data
        let n = {}
        let s = []
        const fl = Math.floor;
        const len = d=>d.length;
        // console.log({a, })
        for(let o=0;o<fl(len(a)/2);o++){
            n[a[o]] = a[fl(len(a)/2) + o]
        }
        for(let r=0;r<len(data);r++){
            s.push(n[i[r]])
        }
        return s.join('').split(',')*/
        $arr = [];
        $arr1 = [];
        $len = strlen($key);
        $dataLen = strlen($data);
        for ($i = 0; $i < ceil($len / 2); $i++) {
            $arr[$key[$i]] = $arr[ceil($len / 2) + $i];
        }
        for ($j = 0; $j < $dataLen; $j++) {
            array_push($arr1, $arr[$data[$j]]);
        }
        var_dump($arr1);exit;
    }
}