<?php


namespace App\Packages\ETL\src\Extracts;


interface ExtractInterface
{
    /**
     * 提取
     *
     * @return mixed
     */
    public function Extract($file);
}