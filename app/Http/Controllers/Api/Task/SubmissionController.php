<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    use GeneralTrait;

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file_task'=> ['required','file','mimes:rar,zip,pdf'],
            'uuid'=>['required','string','exists:tasks,uuid'],
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null,false,$validator->errors(),422);
        }

        try{
            $task_id = Task::where('uuid',$request->uuid)->first()->id ;
            $request->merge([
                'task_id' => $task_id,
                'student_id' => $request->user()->student->id,
            ]);
            $uuid = Str::uuid();
            $data = $request->except('file_task');
            $data['uuid'] = $uuid;

            $file = $request->file('file_task');
            $path =$file->store('tasksSubmission');

            $data ['file']=$path;

            $submission = TaskSubmission::create($data);
            return $this->apiResponse('Success upload task submission');

        }catch(\Exception $e){
            return $this->apiResponse(null,false,$e->getMessage(),500);
        }
    }

    public function submissionsForTask(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'uuid'=>['required','string','exists:tasks,uuid'],
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null,false,$validator->errors(),422);
        }
        $task = Task::whereUuid($request->uuid)->first()->id;
        $files = TaskSubmission::whereTaskId($task)->get()->unique('file');
        return $this->apiResponse(SubmissionResource::collection($files));
    }

}
