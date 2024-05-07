<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use GeneralTrait ;

    public function showByYear(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'year' => ['required','integer','min:1','max:5']
        ]);

        if ($validation->fails()) {
            return $this->apiResponse(null,false,$validation->errors(),422);
        }

        try{
            $year = $request->year;

            $categories = Category::where('year', $year)->get();

            $collection =  CategoryResource::collection($categories);

            return $this->apiResponse($collection);

        }catch(\Exception $e){
            return $this->apiResponse(null,false,$e->getMessage(),500);
        }
    }
}
