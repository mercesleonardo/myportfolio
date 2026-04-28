<?php

namespace App\Http\Resources;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Profile */
class ProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $avatarUrl = filled($this->avatar_path)
            ? Storage::disk('public')->url((string) $this->avatar_path)
            : null;

        $resumeUrl = filled($this->resume_path)
            ? Storage::disk('public')->url((string) $this->resume_path)
            : null;

        return [
            'id'                 => $this->id,
            'user_id'            => $this->user_id,
            'professional_title' => $this->professional_title,
            'bio'                => $this->bio,
            'avatar_path'        => $this->avatar_path,
            'avatar_url'         => $avatarUrl,
            'resume_path'        => $this->resume_path,
            'resume_url'         => $resumeUrl,
            'created_at'         => optional($this->created_at)->toISOString(),
            'updated_at'         => optional($this->updated_at)->toISOString(),
        ];
    }
}
