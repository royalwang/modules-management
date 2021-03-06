<?php namespace WebEd\Base\ModulesManagement\Repositories;

use WebEd\Base\Repositories\Eloquent\EloquentBaseRepositoryCacheDecorator;

use WebEd\Base\ModulesManagement\Repositories\Contracts\PluginsRepositoryContract;

class PluginsRepositoryCacheDecorator extends EloquentBaseRepositoryCacheDecorator implements PluginsRepositoryContract
{
    /**
     * @param $alias
     * @return mixed|null
     */
    public function getByAlias($alias)
    {
        return $this->model->where('alias', '=', $alias)->first();
    }
}
