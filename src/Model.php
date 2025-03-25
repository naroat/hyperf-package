<?php

namespace Naroat\HyperfPackage;

use Naroat\HyperfPackage\Traits\RepositoryTrait;

class Model extends \Hyperf\Database\Model\Model
{
    use RepositoryTrait;

    /**
     * 默认每页记录数.
     */
    public const PAGE_SIZE = 15;

    /**
     * 隐藏的字段列表.
     * @var string[]
     */
    protected array $hidden = ['deleted_at'];
}