<?php namespace WebEd\Base\ModulesManagement\Http\DataTables;

use WebEd\Base\Http\DataTables\AbstractDataTables;
use Yajra\Datatables\Engines\CollectionEngine;
use Yajra\Datatables\Engines\EloquentEngine;
use Yajra\Datatables\Engines\QueryBuilderEngine;

class CoreModulesListDataTable extends AbstractDataTables
{
    protected $repository;

    public function __construct()
    {
        $this->repository = get_core_module();
    }

    /**
     * @return array
     */
    public function headings()
    {
        return [
            'name' => [
                'title' => trans('webed-modules-management::datatables.heading.name'),
                'width' => '20%',
            ],
            'description' => [
                'title' => trans('webed-modules-management::datatables.heading.description'),
                'width' => '50%',
            ],
            'actions' => [
                'title' => trans('webed-core::datatables.heading.actions'),
                'width' => '30%',
            ],
        ];
    }

    /**
     * @return array
     */
    public function columns()
    {
        return [
            ['data' => 'name', 'name' => 'name', 'searchable' => false, 'orderable' => false],
            ['data' => 'description', 'name' => 'description', 'searchable' => false, 'orderable' => false],
            ['data' => 'actions', 'name' => 'actions', 'searchable' => false, 'orderable' => false],
        ];
    }

    /**
     * @return string
     */
    public function run()
    {
        $this->setAjaxUrl(route('admin::core-modules.index.post'), 'POST');

        return $this->view();
    }

    /**
     * @return CollectionEngine|EloquentEngine|QueryBuilderEngine|mixed
     */
    protected function fetchDataForAjax()
    {
        return datatable()->of($this->repository)
            ->rawColumns(['description', 'actions'])
            ->editColumn('description', function ($item) {
                return array_get($item, 'description') . '<br><br>'
                    . trans('webed-modules-management::datatables.author') . ': <b>' . array_get($item, 'author') . '</b><br>'
                    . trans('webed-modules-management::datatables.version') . ': <b>' . get_core_module_composer_version(array_get($item, 'repos')) . '</b><br>'
                    . trans('webed-modules-management::datatables.installed_version') . ': <b>' . (array_get($item, 'installed_version', '...')) . '</b>';
            })
            ->addColumn('actions', function ($item) {
                $updateBtn = version_compare(array_get($item, 'installed_version'), get_core_module_composer_version(array_get($item, 'repos')), '<')
                    ? form()->button(trans('webed-modules-management::datatables.update'), [
                        'title' => trans('webed-modules-management::datatables.update_this_module'),
                        'data-ajax' => route('admin::core-modules.update.post', [
                            'module' => array_get($item, 'alias'),
                        ]),
                        'data-method' => 'POST',
                        'data-toggle' => 'confirmation',
                        'class' => 'btn btn-outline purple btn-sm ajax-link',
                    ])
                    : '';

                return $updateBtn;
            });
    }
}
