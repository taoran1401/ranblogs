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
namespace App\Model;

use Taoran\HyperfPackage\Traits\RepositoryTrait;

class Collect extends Model
{
    use RepositoryTrait;


    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collect';

    //隐藏字段
    protected $hidden = [
        'is_on'
    ];
    /**
     * field is_show
     */
    const IS_SHOW_FALSE = 0;
    const IS_SHOW_TRUE = 1;
    protected static $is_show = [
        self::IS_SHOW_FALSE => '隐藏',
        self::IS_SHOW_TRUE => '显示'
    ];

    /**
     * 分类表
     *
     * @return \Hyperf\Database\Model\Relations\HasOne
     */
    public function collectCategory()
    {
        return $this->hasOne(\App\Model\CollectCategory::class, 'id', 'cat_id')->where('is_on', 1);
    }

    /**
     * 标签表
     *
     * @return \Hyperf\Database\Model\Relations\HasOne
     */
    public function collectTags()
    {
        return $this->belongsToMany(\App\Model\CollectTag::class, 'collect_collect_tag', 'collect_id', 'tag_id')->where('is_on', 1);
    }
}
