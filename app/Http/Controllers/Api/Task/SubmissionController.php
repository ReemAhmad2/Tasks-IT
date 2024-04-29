<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use App\Models\Student;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\DB;
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

    public function addPartners(Request $request)
    {
        try{

            $validator = Validator::make($request->all(),[
                'uuid'=>['required','string','exists:tasks,uuid'],
                'partners' =>['nullable' , 'array'],
                'partners.*' => ['integer' , 'exists:students,number_of_student']
            ]);


            $task = Task::whereUuid($request->uuid)->firstOrFail();

            $size = count($request->partners);

            if($size >= $task->max_student_count){
                return $this->apiResponse(null,false,"cant add all students",422);
            }
            $categories = $task->categories;

            $ids=[];

            foreach ($categories as $category){
                $ids[]= $category->id;
            }
            if($request->partners == null)
            {
                return $this->apiResponse("task is for one student");
            }


            foreach($request->partners as $partner){
                $student = Student::where('number_of_student' , $partner)->first();

                if (! in_array($student->category_id,$ids)){
                    return $this->apiResponse(null,false,'cant add student '.$partner,422);
                }
                if($student->submissions()->where('task_id', $task->id)->exists()){
                    return $this->apiResponse(null,false,'student '.$partner.' already have submission for task',422);
                }
            }

            $user = $request->user();
            $student_submissions = $user->student->submissions;
            $submission = $student_submissions->where('task_id', $task->id)->first();

            $partners = array_unique($request->partners);
            $data = [
                    'file'=>$submission->file,
                    'task_id' => $task->id,
                    'uuid' => Str::uuid(),
                ];

            DB::transaction(function () use ($partners ,$data,$task){

                foreach($partners as $partner){
                    $student = Student::where('number_of_student',$partner)->first();
                    if (!$student->submissions()->where('task_id', $task->id)->exists()) {
                        $student->submissions()->create($data);
                    }
                }
            });

            return $this->apiResponse('Successfully add partners to your tasks');

        }catch(\Exception $e)
        {
            if ($validator->fails()) {
                return $this->apiResponse(null,false,$validator->errors(),422);
            }
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
