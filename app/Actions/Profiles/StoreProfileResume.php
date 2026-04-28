<?php

namespace App\Actions\Profiles;

use App\Models\{Profile, User};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StoreProfileResume
{
    public function handle(User $user, ?UploadedFile $resume, ?Profile $existingProfile = null): ?string
    {
        if (!$resume instanceof UploadedFile) {
            return $existingProfile?->resume_path;
        }

        if ($existingProfile && filled($existingProfile->resume_path)) {
            Storage::disk('public')->delete((string) $existingProfile->resume_path);
        }

        return $resume->storePublicly("profiles/{$user->id}/resume", 'public');
    }
}
