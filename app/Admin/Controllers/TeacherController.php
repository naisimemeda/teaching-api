<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\AdminMessage;
use App\Admin\Extensions\Tools\Notification;
use App\Models\Teacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class TeacherController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '教师管理';

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
        $grid = new Grid(new Teacher());

        $grid->id('id');
        $grid->name('名称');
        $grid->email('email');
        $grid->created_at('created_at');
        $grid->updated_at('updated_at');
        $grid->line_name('Line 名称');
        $grid->line_avatar_url('头像')->image();
        $grid->disableCreateButton();
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
        $form = new Form(new Teacher());
        $form->text('name', __('名称'));
        $form->text('line_name', __('Line 名称'));
        $form->image('line_avatar_url', __('Line 头像'));
        $form->saved(function (Form $form) {
            if ($form->line_avatar_url) {
                $teacher = Teacher::query()->find($form->model()->id);
                $teacher->update([
                    'line_avatar_url' => config('filesystems.disks.qiniuadmin.http_url') . '/' . $teacher->line_avatar_url
                ]);
            }
        });
        return $form;
    }
}
