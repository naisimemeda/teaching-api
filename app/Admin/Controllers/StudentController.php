<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\AdminMessage;
use App\Models\Student;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class StudentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '学生列表';

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
        $grid = new Grid(new Student());

        $grid->column('id', __('Id'));
        $grid->teacher()->name('学校管理员');
        $grid->school()->name('学校管理员');
        $grid->column('name', __('学生名称'));
        $grid->column('avatar_url', __('头像'))->image();
        $grid->column('account', __('账号'));
        $grid->column('line_name', __('Line '));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->add(new AdminMessage);
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
        $form = new Form(new Student());

        $form->text('name', __('名称'));
        $form->image('avatar_url', __('头像'));
        $form->text('account', __('账号'));
        $form->saved(function (Form $form) {
            if ($form->avatar_url) {
                $student = Student::query()->find($form->model()->id);
                $student->update([
                    'avatar_url' => config('filesystems.disks.qiniuadmin.http_url') . '/' . $student->avatar_url
                ]);
            }
        });
        return $form;
    }
}
