<?php


namespace App\Packages\DataInterface\src;


class Export
{
    /**
     * 导出csv
     *
     * @param $file 文件
     * @param $data 数据
     * @param bool $isHeader 是否写入文件头
     * @return bool
     * @throws \Exception
     */
    public function csv($file, $data, $isHeader = true)
    {
        $file = fopen($file, 'a');

        if (!$file) {
            throw new \Exception('文件');
        }

        if ($isHeader) {
            fputcsv($file, ['name', 'date', 'value']);
        }

        foreach ($data as $val) {
            $formatData = $val['all']['formatData'];
            foreach ($formatData as $_val) {
                fputcsv($file, [$_val['word'], $_val['date'], $_val['value']]);
            }
        }

        fclose($file);

        return true;
    }
}