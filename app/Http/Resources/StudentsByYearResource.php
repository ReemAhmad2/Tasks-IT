<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentsByYearResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'uuid' => $this->uuid,
            'number_university' => $this->number_of_student,
            'year' => $this->category->year,
            'category'=> $this->category->number,
        ];
    }
}
