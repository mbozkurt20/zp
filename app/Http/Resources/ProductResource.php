<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'id' => $this->id,
          'category_id' => $this->category_id??null,
          'category_name' => $this->category->name??null,
          'name' => $this->name,
          'is_day' => $this->is_day,
          'image' => $this->image,
          'file' => $this->file,
          'description' => $this->description,
          'created_at' => $this->created_at,
        ];
    }
}
