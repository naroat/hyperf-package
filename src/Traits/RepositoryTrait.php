<?php


namespace Taoran\HyperfPackage\Traits;

use Taoran\HyperfPackage\Traits\CallbackTrait;

/**
 * repository层，协助model处理
 *
 * Trait RepositoryTrait
 * @package App\Traits
 */
trait RepositoryTrait
{
    /**
     * 获取列表
     *
     * @param array $select
     * @param bool $is_all
     * @return \Hyperf\Contract\LengthAwarePaginatorInterface
     */
    public function getList($select = ['*'], $params, callable $where = null)
    {
        $orm = self::select($select)->where('is_on', 1);

        //执行callable
        CallbackTrait::callback($where, $orm);

        if (isset($params['is_all']) && $params['is_all'] == 1) {
            $list = $orm->get();
        } else {
            $list = $orm->paginate($params['page_limit'] ?? 20);
        }
        return $list;
    }

    /**
     * 根据id获取数据
     *
     * @param $id
     */
    public function getOneById($select = ['*'], $id)
    {
        $data = self::select($select)->where('is_on', 1)->find($id);
        if (!$data) {
            throw new \Exception("数据不存在！");
        }
        return $data;
    }

    /**
     * 获取单条数据
     *
     * @param array $select
     * @param string $where
     * @return \Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|object|null
     */
    public function getOne($select = ['*'], callable $where = null)
    {
        $orm =  self::select($select)->where('is_on', 1);

        CallbackTrait::callback($where, $orm);

        return $orm->first();
    }
}