<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskStatusResource extends JsonResource
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
            'uuid'=>$this->uuid,
            'description'=>$this->description,
            'since' => $this->duration,
            'deadline'=>$this->deadline->format('Y-m-d'),
            'subject'=>$this->subject->name,
            'teacher' =>$this->teacher->user->name,
            'status'=>$this->status,
        ];
    }
}
