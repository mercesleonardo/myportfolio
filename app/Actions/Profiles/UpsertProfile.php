<?php

namespace App\Actions\Profiles;

use App\Models\{Profile, User};
use Illuminate\Http\UploadedFile;

class UpsertProfile
{
    public function __construct(
        public StoreProfileAvatar $storeProfileAvatar,
        public StoreProfileResume $storeProfileResume,
    ) {
    }

    /**
     * @param  array{professional_title?: string|null, bio?: string|null}  $data
     */
    public function handle(User $user, array $data, ?UploadedFile $avatar = null, ?UploadedFile $resume = null): Profile
    {
        $existingProfile = $user->profile;

        $avatarPath = $this->storeProfileAvatar->handle($user, $avatar, $existingProfile);
        $resumePath = $this->storeProfileResume->handle($user, $resume, $existingProfile);

        return Profile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'professional_title' => $data['professional_title'] ?? $existingProfile?->professional_title,
                'bio'                => $data['bio'] ?? $existingProfile?->bio,
                'avatar_path'        => $avatarPath,
                'resume_path'        => $resumePath,
            ],
        );
    }
}
