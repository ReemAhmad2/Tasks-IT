<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Http\Traits\GeneralTrait;


class AccessToSubmission
{
    use GeneralTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $task = Task::where('uuid',$request->uuid)->first();
        if($task ==null){
            return $this->apiResponse(null,false,'not found',401);
        }
        $ids =[];
        foreach($task->categories as $category){
            $ids[] =$category->id;
        }
        if(! in_array($user->student->category_id,$ids)){
            return $this->apiResponse(null,false,'you are not allowed to submition',403);
        }
        //  $user->student->submissions
        // return $this->apiResponse($user->student->submissions);
        foreach ($user->student->submissions as $submission){
            if($submission->task_id == $task->id)
            {
                return $this->apiResponse(null,false,'cant upload ',403);
            }
        }
        return $next($request);
    }
}
