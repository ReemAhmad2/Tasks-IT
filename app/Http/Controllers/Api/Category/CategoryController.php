<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    use GeneralTrait;

    public function showByYear(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'year' => ['required', 'integer', 'min:1', 'max:5']
        ]);

        if ($validation->fails()) {
            return $this->apiResponse(null, false, $validation->errors(), 422);
        }

        try {
            $year = $request->year;

            $categories = Category::where('year', $year)->get();

            $collection =  CategoryResource::collection($categories);

            return $this->apiResponse($collection);
        } catch (\Exception $e) {
            return $this->apiResponse(null, false, $e->getMessage(), 500);
        }
    }

    public function add(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'year' => ['required', 'integer', 'min:1', 'max:5'],
            'number' => ['required', 'integer', 'min:1']
        ]);

        if ($validation->fails()) {
            return $this->apiResponse(null, false, $validation->errors(), 422);
        }

        try {
            $year = $request->year;
            $number = $request->number;

            DB::transaction(function () use ($year, $number) {
                for ($i = 1; $i <= $number; $i++) {
                    $data = [
                        'year' => $year,
                        'number' => $i,
                        'uuid' => Str::uuid()
                    ];

                    $unique = Category::where('year', $year)->where('number', $i)->first();

                    if ($unique == null) {
                        $category = Category::create($data);
                    }
                }
            });

            return $this->apiResponse('success add category');
        } catch (\Exception $e) {
            return $this->apiResponse(null, false, $e->getMessage(), 500);
        }
    }

    public function delete()
    {
        try {
            Category::query()->delete();
            return $this->apiResponse('Success delete all categories');
        } catch (\Exception $e) {
            return $this->apiResponse(null, false, $e->getMessage(), 500);
        }
    }

    public function deleteCategory(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'year' => ['required', 'integer', 'min:1', 'max:5'],
            'category' => ['required', 'integer', 'min:1', 'exists:categories,number']
        ]);

        if ($validation->fails()) {
            return $this->apiResponse(null, false, $validation->errors(), 422);
        }

        
        try {

            $category = Category::where('year', $request->year)->where('number', $request->category)->firstOrFail();

            $category->delete();

            return $this->apiResponse('delete category successfully');

        } catch (\Exception $e) {
            return $this->apiResponse(null, false, $e->getMessage(), 500);
        }
    }
}
