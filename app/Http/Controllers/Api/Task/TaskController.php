<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    use GeneralTrait;


    public function store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'description' => 'required|string',
            'max_student_count' => 'integer|min:1',
            'deadline' => 'required|date|after:today',
            'subject' => 'required|string|exists:subjects,uuid',
            'category' => 'required|array',
            'category.*' => 'required|string|exists:categories,uuid',
        ]);

        if ($validate->fails()) {
            return $this->apiResponse(null,false,$validate->errors(),422);
        }
        try {

            $subject = Subject::where('uuid',$request->subject)->first();
            $year = $subject->year;
            $Y_category = Category::where('year',$year)->pluck('uuid')->toArray();
            $ids = [];
            foreach ($request->category as $category)
            {
                if( !in_array($category,$Y_category))
                {
                    return $this->apiResponse(null,false,'Enter Category True Please',422);
                }
                $id_category = Category::where('uuid',$category)->first()->id;
                $ids[]=$id_category;
            }
            // return $ids;

            $teacher = $request->user()->teacher->id;

            $task = new Task ;
            $task->uuid= Str::uuid();
            $task->teacher_id = $teacher;
            $task->description = $request->description;
            $task->deadline = $request->deadline;
            $task->subject_id = $subject->id;
            $task->max_student_count = $request->max_student_count ;

            $task->save();
            $task->categories()->attach($ids);

            return $this->apiResponse("Successfully Add Task");

            }catch(\Exception $e){
                return $this->apiResponse(null,false,$e,500);
            }
    }

    public function deleteTask($uuid)
    {
        try{
            $task =Task::where('uuid', $uuid)->firstOrFail();
        }catch(\Exception $e){
            return $this->apiResponse(null,false,"Not found ",422);
        }

        $user = Auth::user();

        if($user->type == 'admin')
        {
            $task->delete();
            return $this->apiResponse("Task Successfully Deleted");
        }
        
        $teacher = $user->teacher->tasks;
        foreach ($teacher as $task_teacher)
        {
            if($task->uuid == $task_teacher->uuid){
                $task->delete();
                return $this->apiResponse("Task Successfully Deleted");

            }
        }
        return $this->apiResponse(null,false,'you cant deleted this task',422);

    }
}
