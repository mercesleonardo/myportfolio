<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Users\{CreateUser, DeleteUser, RestoreUser, UpdateUser};
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\{StoreUserRequest, UpdateUserRequest};
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\{RedirectResponse, Request};
use Inertia\{Inertia, Response};

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $usersQuery = User::query()->latest();

        $trashed = (string) $request->query('trashed', 'with');

        if ($trashed === 'only') {
            $usersQuery->onlyTrashed();
        } elseif ($trashed === 'without') {
            $usersQuery->withoutTrashed();
        } else {
            $usersQuery->withTrashed();
        }

        if ($role = $request->string('role')->toString()) {
            $usersQuery->where('role', $role);
        }

        $active = (string) $request->query('active', 'all');

        if ($active === '1') {
            $usersQuery->where('is_active', true);
        } elseif ($active === '0') {
            $usersQuery->where('is_active', false);
        }

        if ($q = $request->string('q')->toString()) {
            $usersQuery->where(function ($query) use ($q): void {
                $query
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        $users = $usersQuery->paginate(15)->withQueryString();

        return Inertia::render('admin/users/Index', [
            'users'   => UserResource::collection($users),
            'roles'   => array_map(fn (UserRole $r) => ['value' => $r->value, 'label' => $r->label()], UserRole::cases()),
            'filters' => [
                'q'       => $request->query('q'),
                'role'    => $request->query('role'),
                'active'  => $request->query('active', 'all'),
                'trashed' => $request->query('trashed', 'with'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/users/Create', [
            'roles' => array_map(fn (UserRole $r) => ['value' => $r->value, 'label' => $r->label()], UserRole::cases()),
        ]);
    }

    public function store(StoreUserRequest $request, CreateUser $action): RedirectResponse
    {
        $action->handle($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User created.')]);

        return to_route('admin.users.index');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('admin/users/Edit', [
            'user'  => new UserResource($user),
            'roles' => array_map(fn (UserRole $r) => ['value' => $r->value, 'label' => $r->label()], UserRole::cases()),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUser $action): RedirectResponse
    {
        $action->handle($user, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User updated.')]);

        return to_route('admin.users.edit', $user);
    }

    public function destroy(Request $request, User $user, DeleteUser $action): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $user->id) {
            Inertia::flash('toast', ['type' => 'error', 'message' => __('You cannot delete your own account.')]);

            return to_route('admin.users.edit', $user);
        }

        $action->handle($user);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User deleted.')]);

        return to_route('admin.users.index');
    }

    public function restore(User $user, RestoreUser $action): RedirectResponse
    {
        $action->handle($user);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User restored.')]);

        return to_route('admin.users.edit', $user);
    }
}
