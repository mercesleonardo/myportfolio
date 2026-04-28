<?php

namespace App\Http\Controllers;

use App\Actions\Profiles\UpsertProfile;
use App\Http\Requests\ProfessionalProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\RedirectResponse;
use Inertia\{Inertia, Response};

class ProfessionalProfileController extends Controller
{
    public function edit(): Response
    {
        $user = request()->user()->load('profile');

        return Inertia::render('ProfessionalProfile', [
            'profile' => $user->profile ? new ProfileResource($user->profile) : null,
        ]);
    }

    public function update(ProfessionalProfileUpdateRequest $request, UpsertProfile $action): RedirectResponse
    {
        $action->handle(
            $request->user(),
            $request->validated(),
            $request->file('avatar'),
            $request->file('resume'),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Professional profile updated.')]);

        return to_route('professional-profile.edit');
    }
}
