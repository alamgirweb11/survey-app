<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class SurveyDashboardResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'image_url' =>  $this->image ? URL::to($this->image) : null,
            'slug' => $this->slug,
            'status' => $this->status !== 'draft',
            'created_at' => (new DateTime($this->created_at))->format('d-m-Y h:i:s a'),
            'expire_date' => (new DateTime($this->expire_date))->format('d-m-Y h:i:s a'),
            'questions' => $this->questions->count(),
            'answers' => $this->answers->count(),
        ];
    }
}
