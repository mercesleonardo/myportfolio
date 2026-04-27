<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'email'             => $this->email,
            'is_active'         => (bool) $this->is_active,
            'role'              => $this->role?->value ?? null,
            'role_label'        => $this->role?->label() ?? null,
            'google_id'         => $this->google_id,
            'locale'            => $this->locale,
            'deleted_at'        => optional($this->deleted_at)->toISOString(),
            'email_verified_at' => optional($this->email_verified_at)->toISOString(),
            'created_at'        => optional($this->created_at)->toISOString(),
            'updated_at'        => optional($this->updated_at)->toISOString(),
        ];
    }
}
