<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'type' => $this->type,
            'service_unit' => $this->serviceUnit ? [
                'id' => $this->serviceUnit->id,
                'name' => $this->serviceUnit->name,
            ] : null,
            'homecell' => $this->homecell ? [
                'id' => $this->homecell->id,
                'name' => $this->homecell->name,
            ] : null,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
