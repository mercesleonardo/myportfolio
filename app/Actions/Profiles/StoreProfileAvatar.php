<?php

namespace App\Actions\Profiles;

use App\Models\{Profile, User};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StoreProfileAvatar
{
    public function handle(User $user, ?UploadedFile $avatar, ?Profile $existingProfile = null): ?string
    {
        if (!$avatar instanceof UploadedFile) {
            return $existingProfile?->avatar_path;
        }

        if ($existingProfile && filled($existingProfile->avatar_path)) {
            Storage::disk('public')->delete((string) $existingProfile->avatar_path);
        }

        return $avatar->storePublicly("profiles/{$user->id}/avatar", 'public');
    }
}
