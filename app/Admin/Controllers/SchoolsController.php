<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\AdminMessage;
use App\Models\School;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class SchoolsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '学校管理';

    public function index(Content $content)
    {
        return $content->title($this->title)->description(' ')->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new School());

        $grid->column('id', __('Id'));
        $grid->column('name', __('名称'));
        $grid->column('cover', __('图片'))->image();
        $grid->admin()->name('学校管理员');
        $states = [
            'on' => ['value' => 1, 'text' => '正常', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default'],
        ];
        $grid->column('status', __('审核'))->switch($states);

        $grid->column('created_at', __('添加时间'));
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new School());
        $form->text('name', __('学习名称'));
        $form->image('cover', __('封面'));
        $form->switch('status', __('状态'));
        $form->saved(function (Form $form) {
            if ($form->cover) {
                $school = School::query()->find($form->model()->id);
                $school->update([
                    'cover' => config('filesystems.disks.qiniuadmin.http_url') . '/' . $school->cover
                ]);
            }
        });
        return $form;
    }
}
