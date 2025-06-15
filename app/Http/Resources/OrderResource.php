<?php

namespace App\Http\Resources;

use App\Models\Basket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user' => $this->user->name,
            'created_at' => date('d-m-Y H:i:s', strtotime($this->created_at)),
            'is_ready' => $this->is_ready,
            'ready_date' => $this->ready_date ? date('d-m-Y H:i:s', strtotime($this->ready_date)) : '-',
        ];
    }
}
