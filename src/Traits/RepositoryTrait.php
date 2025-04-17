<?php


namespace Naroat\HyperfPackage\Traits;


/**
 * repository，协助model处理
 *
 * Trait RepositoryTrait
 */
trait RepositoryTrait
{
    use CallbackTrait;

    public function getList($params, $select = ['*'], callable $where = null)
    {
        $orm = self::select($select);

        //callable
        $this->callback($where, $orm);

        if (isset($params['is_all']) && $params['is_all'] == 1) {
            $list = $orm->get();
        } else {
            $list = $orm->paginate($params['page_limit'] ?? self::PAGE_SIZE, ['*'], 'page', $params['page'] ?? 1);
        }

        return $list;
    }

    /**
     * 根据id获取数据
     *
     * @param $id
     * @param $select
     * @return \Hyperf\Database\Query\Builder|mixed
     * @throws \Exception
     */
    public function getOneById($id, $select = ['*'])
    {
        $data = self::select($select)->find($id);
        if (!$data) {
            throw new \Exception("数据不存在！");
        }
        return $data;
    }

    /**
     * 获取单条数据
     *
     * @param $select
     * @param callable|null $where
     * @return \Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|object|null
     */
    public function getOne($select = ['*'], callable $where = null)
    {
        $orm =  self::select($select);

        //callable
        $this->callback($where, $orm);

        return $orm->first();
    }
}