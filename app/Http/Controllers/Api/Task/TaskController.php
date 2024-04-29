<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskStatusResource;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Teacher;

class TaskController extends Controller
{
    use GeneralTrait;

    public function index ()
    {
        $tasks = TaskResource::collection(Task::all());
        return $this->apiResponse($tasks);
    }

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

    public function show(Request $request)
    {
        $task = Task::where('uuid',$request->uuid)->first();
        $data = TaskResource::make($task);
        return $this->apiResponse($data);
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

    public function allTasksForStudent()
    {
        $user = Auth::user();
        $tasks = $user->student->category->tasks;
        return $this->apiResponse(TaskStatusResource::collection($tasks));
    }

    public function searchTask(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'subject_uuid' => ['nullable','string','exists:subjects,uuid'],
            'teacher_name' => ['nullable','string'],
        ]);

        if ($validate->fails()) {
            return $this->apiResponse(null,false,$validate->errors(),422);
        }
        $name = $request->teacher_name;
        $tasks =TaskResource::collection(Task::join('teachers', 'tasks.teacher_id', '=', 'teachers.id')
        ->join('users', 'teachers.user_id', '=', 'users.id')
        ->where('users.name', 'like', '%'.$name.'%')
        ->select('tasks.*')
        ->get()) ;

        return $this->apiResponse($tasks);
    }
}
