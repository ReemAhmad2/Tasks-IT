<?php

namespace App\Http\Controllers\Api\Subject;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectAndTasksResource;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use App\Models\Subject;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    use GeneralTrait;

    public function index()
    {
        $subjects = Subject::all();
        return $this->apiResponse(SubjectResource::collection($subjects));
    }

    public function subjectsByTerm(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'year' => ['required', 'integer' ,'max:5','min:1'],
            'term' => ['required', 'integer' ,'max:2','min:1']
        ]);
        if ($validation->fails()) {
            return $this->apiResponse(null, false, $validation->errors(), 422);
        }
        $year = $request->year;
        $term = $request->term;
        $subjects = Subject::where('year',$year)->where('term',$term)->get();
        $data = SubjectResource::collection($subjects);
        return $this->apiResponse($data);

    }

    public function allTaskBySubject(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'uuid' => ['required', 'string' , 'exists:subjects,uuid']
        ]);
        if ($validation->fails()) {
            return $this->apiResponse(null, false, $validation->errors(), 422);
        }

        $subject = Subject::where('uuid',$request->uuid)->first();
        $tasks = $subject->tasks ;
        $data = TaskResource::collection($tasks);
        return $this->apiResponse($data);

    }

    public function subjectsStudent(Request $request)
    {
        $user = $request->user();
        $tasks = $user->student->category->tasks ;
        $subjects = $tasks->pluck('subject')->unique();
        return SubjectAndTasksResource::collection($subjects);
    }

}
