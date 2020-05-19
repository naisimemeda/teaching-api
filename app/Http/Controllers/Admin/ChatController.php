<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MessageRequest;
use App\Http\Resources\ChatMessages;
use App\Models\ChatLog;
use App\Models\Message;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\ChatService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class ChatController extends Controller
{
    use AuthenticatesUsers;
    /**
     * 聊天用户
     * @param Request $request
     * @return mixed
     */
    public function chatStudent(Request $request)
    {
        $student_ids = ChatLog::query()->where('teacher_id', Auth::id())->pluck('student_id');

        $students = Student::query()->select('id', 'avatar_url', 'name')->whereIn('id', $student_ids);
        if ($id = $request->get('id')) {
            $students = $students->where('id', '!=', $id)->get();

            $student = Student::query()->select('id', 'avatar_url', 'name')->where('id', $request->get('id'))->get();

            $students = $student->merge($students);

            return $this->success($students);
        }

        $results = $students->get();
        return $this->success($results);
    }

    /**
     * 学生聊天记录
     * @param MessageRequest $request
     * @return mixed
     */
    public function chatMessages(MessageRequest $request)
    {
        $id = $request->get('id');

        $student_messages = Message::with(['student'])->where('send_id', $id)->where('receive_id', Auth::id())->latest()->get();

        $teacher_messages = Message::with(['teacher'])->where('send_id', Auth::id())->where('receive_id', $id)->latest()->get();

        $messages = $student_messages->merge($teacher_messages)->sortBy('created_at')->values();

        return $this->success(ChatMessages::collection($messages));
    }

    /**
     * 教师发送消息
     * @param MessageRequest $request
     * @param ChatService $service
     * @return mixed
     */
    public function sendStudentChatMessage(MessageRequest $request, ChatService $service)
    {
        $student_id = $request->get('student_id');

        $message = $request->get('message');

        $teacher = Teacher::query()->find(Auth::id());

        ChatLog::query()->updateOrInsert([
            'student_id' => $request->get('student_id'),
            'teacher_id' => Auth::id()
        ], ['created_at' => now()]);

        $send_message = $service->sendMessage($message, Auth::id(), 'teacher', $teacher, $student_id);

        return $this->success($send_message);
    }

    /**
     * 学生发送信息
     * @param MessageRequest $request
     * @param ChatService $service
     * @return mixed
     */
    public function sendTeacherMessage(MessageRequest $request, ChatService $service)
    {
        $message = $request->get('message');

        $student = Student::query()->find(Auth::id());

        ChatLog::query()->updateOrInsert([
            'student_id' => Auth::id(),
            'teacher_id' => $student->teacher_id
        ], ['created_at' => now()]);

        $send_message = $service->sendMessage($message, Auth::id(), 'student', $student, $student->teacher_id);

        return $this->success($send_message);
    }

    /**
     * 获取学生本人的聊天记录
     * @param Request $request
     * @return mixed
     */
    public function getStudentMessages(Request $request)
    {
        $teacher_id = Student::query()->where('id', Auth::id())->value('teacher_id');

        $student_messages = Message::with(['student'])->where('send_id', Auth::id())->where('receive_id', $teacher_id)->latest()->get();

        $teacher_messages = Message::with(['teacher'])->where('send_id', $teacher_id)->where('receive_id', Auth::id())->latest()->get();

        $messages = $student_messages->merge($teacher_messages)->sortBy('created_at')->values();

        return $this->success(ChatMessages::collection($messages));
    }
}
