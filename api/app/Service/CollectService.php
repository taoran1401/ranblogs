<?php


namespace App\Service;

use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use function Taoran\HyperfPackage\Helpers\get_msec_to_mescdate;
use function Taoran\HyperfPackage\Helpers\set_save_data;


class CollectService
{
    /**
     * @Inject()
     * @var \App\Model\Collect
     */
    protected $collectModel;

    /**
     * @Inject()
     * @var \App\Service\CollectCategoryService
     */
    protected $collectCategoryService;

    /**
     * @Inject()
     * @var \App\Model\CollectTag
     */
    protected $collectTagModel;

    /**
     * @Inject()
     * @var \App\Model\CollectCollectTag
     */
    protected $collectCollectTagModel;

    /**
     * 列表
     *
     * @param array $params
     * @return \Hyperf\Contract\LengthAwarePaginatorInterface
     */
    public function getList($params)
    {
        $list = $this->collectModel->getList(['id', 'title', 'desc', 'cover', 'click_num', 'content_html', 'cat_id', 'is_show', 'created_at', 'updated_at'], $params, function ($query) use ($params) {
            //with
            $query->with('collectCategory')->with('collectTags');
            //where
            if (isset($params['title']) && $params['title'] != '') {
                $query->where('title', 'like', "%{$params['title']}%");
            }
            if (isset($params['is_show']) && $params['is_show'] != '') {
                $query->where('is_show', $params['is_show']);
            }
            if (isset($params['tag_id']) && $params['tag_id'] != '') {
                $query->whereHas('collectTags', function ($_query) use ($params) {
                    $_query->where('id', $params['tag_id']);
                });
            }
            //orderBy
            $query->orderBy('id', 'desc');
        });

        $list->each(function ($item) {
            $item->cate_name = $item->collectCategory->name ?? '';
            $item->content_html = htmlspecialchars_decode($item->content_html);
            $item->is_show_text = \App\Model\Collect::$is_show[$item->is_show];
            unset($item->collectCategory);
        });

        return $list;
    }

    /**
     * 单条
     *
     * @param int $id
     */
    public function getOne(int $id)
    {
        $data = $this->collectModel->getOne(['id', 'title', 'desc', 'cover', 'content_html', 'cat_id', 'is_show'], function ($query) use ($id) {
            //with
            $query->with('collectCategory')->with('collectTags')->where('id', $id);
        });

        if ($data) {
            $data->cat_name = $data->collectCategory->name ?? '';
            $data->content_html = htmlspecialchars_decode($data->content_html);
            unset($data->collectCategory);
        }

        return $data ?? [];
    }

    /**
     * 添加
     */
    public function add($params)
    {
        try {
            //检查分类
            $this->collectCategoryService->check($params['cat_id']);

            //添加文章
            $model = new \App\Model\Collect();
            set_save_data($model, [
                'title' => $params['title'],
                'desc' => $params['desc'],
                'cover' => $params['cover'],
                'content' => $params['content'] ?? '',
                'content_html' => $params['content_html'],
                'cat_id' => $params['cat_id'],
                'is_show' => $params['is_show'] ?? 0,
                'type' => $params['type'] ?? 0,
            ])->save();

            //设置标签
            $this->setTags($params, $model->id);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 更新
     *
     * @param int $id
     */
    public function update(int $id, $params)
    {
        try {
            Db::beginTransaction();
            //检查分类
            $this->collectCategoryService->check($params['cat_id']);
            $data = $this->collectModel->getOneById($id, ['*']);
            set_save_data($data, $params)->save();
            //设置标签
            $this->setTags($params, $data->id);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 删除
     *
     * @param int $id
     */
    public function destroy(int $id)
    {

        try {
            Db::beginTransaction();

            $data = $this->collectModel->getOneById($id, ['*']);
            $data->is_on = 0;
            $data->save();

            //清除tag绑定关系
            $this->collectCollectTagModel->where('collect_id', $data->id)->delete();

            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
            throw new \Exception($e->getMessage());
        }

        return true;
    }

    /**
     * 设置标签
     *
     * @param $params
     */
    public function setTags($params, $collect_id)
    {
        $tags = (isset($params['tags']) && is_array($params['tags'])) ? array_filter($params['tags']) : [];

        //验证
        if (count($tags) == 0) {
            return true;
        }
        //设置标签
        $collect_tag_save = [];
        foreach ($tags as $v) {
            $tag = $this->collectTagModel->getOne(['*'], function ($query) use ($v) {
                $query->where('name', $v);
            });

            if ($tag) {
                //已存在，绑定
                $collect_tag_save[] = [
                    'collect_id' => $collect_id,
                    'tag_id' => $tag->id
                ];
            } else {
                //不存在，新增
                $tabObj = new \App\Model\CollectTag();
                set_save_data($tabObj, [
                    'name' => $v
                ]);
                $tabObj->save();
                //绑定
                $collect_tag_save[] = [
                    'collect_id' => $collect_id,
                    'tag_id' => $tabObj->id
                ];
            }
        }
        //清除tag绑定关系
        $this->collectCollectTagModel->where('collect_id', $collect_id)->delete();
        //写入tag绑定
        $this->collectCollectTagModel->insert($collect_tag_save);
    }

    /**
     * 归档
     *
     * @return array
     */
    public function Archive()
    {
        $list = $this->collectModel->getList(['id', 'title', 'created_at', 'updated_at'], [], function ($query) {
            $query->where('is_show', 1)->orderBy('created_at', 'DESC');
        });

        //重装数据
        $group_list = [];
        $list->each(function ($item, $key) use (&$group_list) {
            $year = get_msec_to_mescdate($item->created_at, "Y");
            $item->title = htmlspecialchars_decode($item->title, ENT_QUOTES);
            $item->created_at_format = get_msec_to_mescdate($item->created_at, 'Y/m/d');
            $group_list[$year][] = $item;
        });

        //组合数据方便前端使用
        $group_list_full = [];
        foreach ($group_list as $key => $val) {
            $group_list_full[] = [
                'year' => $key,
                'list' => $group_list[$key]
            ];
        }

        return $group_list_full;
    }
}