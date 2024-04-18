<?php

namespace App\Http\Middleware;

use App\Models\Task;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\GeneralTrait;

class AccessToTask
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
        if($task ==null)
        {
            return $this->apiResponse(null,false,'not found',401);
        }
        if($user->type == 'teacher')
        {
            if($task->teacher_id == $user->teacher->id)
            {
                return $next($request);
            }
            return $this->apiResponse(null,false,'you are not allowed to access this',403);
        }
        if($user->type == 'student')
        {
            $ids =[];
            foreach($task->categories as $category){
                $ids[] =$category->id;
            }
            if(in_array($user->student->category_id,$ids)){
                return $next($request);
            }
            return $this->apiResponse(null,false,'you are not allowed to access this',403);
        }
        return $next($request);
    }
}
