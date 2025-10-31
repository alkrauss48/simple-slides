<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PresentationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'content' => $this->resource->content,
            'slide_delimiter' => $this->resource->slide_delimiter,
            'is_published' => $this->resource->is_published,
            'user' => [
                'username' => $this->resource->user->username,
                'name' => $this->resource->user->name,
            ],
        ];
    }
}
