<?php

namespace App\Admin\Actions;

use App\Events\AdminMessageEvent;
use App\Services\ChatService;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdminMessage extends RowAction
{
    public $name = '发送信息';

    public function handle(Model $model, Request $request)
    {
        $message = $request->get('message');
        $type = $request->get('type');
        $receive_type = $request->get('receive_type');
        if ($type[0] == 'web') {
            event(new AdminMessageEvent($message, 'admin', $receive_type, $model->id));
        } else {
            app(ChatService::class)->lineNotification($message, $model->line_id);
        }
        if ($type[1] == 'line') {
            app(ChatService::class)->lineNotification($message, $model->line_id);
        }
        return $this->response()->success('Success message.')->refresh();
    }


    public function form()
    {
        $type = [
            'web' => '网页',
            'line' => 'line',
        ];

        $this->checkbox('type', '类型')->rules('required')->options($type);
        $this->hidden('receive_type')->default(request()->route()->getName());
        $this->textarea('message', '消息')->rules('required');
    }
}
