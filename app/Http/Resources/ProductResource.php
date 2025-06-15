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
          'image' => $this->image,
          'description' => $this->description,
          'price' => $this->price,
          'discount' => $this->discount,
          'tax' => $this->tax,
          'qr_code' => $this->qr_code,
          'barcode' => $this->barcode,
          'stock_type' => $this->stock_type,
          'quantity' => $this->quantity,
          'warning_quantity' => $this->warning_quantity,
          'created_at' => $this->created_at,
        ];
    }
}
