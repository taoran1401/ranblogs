<?php


namespace App\Packages\ETL\src\Extracts;


class Csv implements ExtractInterface
{

    /**
     * 从csv中提取数据
     *
     * @param $file
     * @return array|string
     */
    public function extract($file)
    {
        //文件验证
        $ext = $this->getExt($file);
        if ($ext != 'csv') {
            return '文件格式错误！';
        }

        //获取内容
        $content = [];
        $file = fopen($file, "r");
        while (!feof($file)) {
            $content[] = fgetcsv($file);
        }
        fclose($file);

        return $content;
    }

    public function getExt($file)
    {
        $ext = explode('.', $file);
        return $ext[count($ext) - 1];
    }
}