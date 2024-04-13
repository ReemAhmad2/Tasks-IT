<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\GeneralTrait;
class TypeUser
{
    use GeneralTrait ;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next , ...$types)
    {
        $user = $request->user();

        if($user === null)
        {
            return $this->apiResponse(null,false,'UnAuthenticated',402);
        }

        if(! in_array($user->type,$types))
        {
            return $this->apiResponse(null,false,'cant Access',403);
        }

        return $next($request);
    }
}
