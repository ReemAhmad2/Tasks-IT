<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Support\Str;

class CommentController extends Controller
{

    use GeneralTrait;

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'uuid' => ['required','string','exists:tasks,uuid'],
            'comment_text' => ['required','string'],
        ]);

        if ($validate->fails()) {
            return $this->apiResponse(null,false,$validate->errors(),422);
        }

        try{
            $task_id = Task::where('uuid',$request->uuid)->firstOrFail()->id;
            $user_id = $request->user()->id;

            $comment = new Comment() ;
            $comment->user_id = $user_id;
            $comment->task_id = $task_id;
            $comment->uuid = Str::uuid();
            $comment->comment_text = $request->comment_text;
            $comment->save();
            return $this->apiResponse('Success Add Comment');

        }catch(\Exception $e)
        {
            return $this->apiResponse(null,false,$e->getMessage(),422);
        }
    }

    public function delete(Request $request)
    {
        $user = $request->user();
        $comment = Comment::where('uuid',$request->uuid)->first();
        if($comment == null)
        {
            return $this->apiResponse(null,false,'Comment Not Found',403);
        }
        if($user->type =='teacher')
        {
            if($comment->task->teacher_id == $user->teacher->id)
            {
                $comment->delete();
                return $this->apiResponse('Deleted Success');
            }
            return $this->apiResponse(null,false,'You are not Owner this Task So You cant delete this comment',403);
        }
        if($user->type == 'student')
        {
            if($comment->user->student->id == $user->student->id)
            {
                $comment->delete();
                return $this->apiResponse('Deleted Success');
            }
            return $this->apiResponse(null,false,'You cant delete this comment',403);
        }
    }
}
