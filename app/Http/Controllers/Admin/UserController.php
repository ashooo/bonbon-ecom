<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserStatusAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function buildUserRedirectQuery(Request $request): array
    {
        return array_filter([
            'section' => 'users',
            'user_status' => $request->string('user_status')->value(),
            'user_search' => $request->string('user_search')->trim()->value(),
            'user_page' => $request->integer('user_page') ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('admin.dashboard', $this->buildUserRedirectQuery($request));
    }

    public function show(Request $request, User $user)
    {
        $user = User::withTrashed()->findOrFail($user->id);

        $user->load([
            'orders' => fn ($query) => $query
                ->with('items.variant.product')
                ->latest()
                ->take(20),
            'statusAudits.actor',
        ]);

        $backQuery = $this->buildUserRedirectQuery($request);

        return view('admin.users.show', compact('user', 'backQuery'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($user->id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user->id)],
        ]);

        $user->update([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => isset($data['phone']) && trim((string) $data['phone']) !== '' ? trim((string) $data['phone']) : null,
        ]);

        return redirect()
            ->route('admin.users.show', ['user' => $user] + $this->buildUserRedirectQuery($request))
            ->with('success', 'User details updated successfully.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($user->id);

        $data = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        if ((int) $user->id === (int) $request->user()->id && ! $data['is_active']) {
            return redirect()
                ->route('admin.dashboard', $this->buildUserRedirectQuery($request))
                ->withErrors(['is_active' => 'You cannot deactivate your own admin account.']);
        }

        $previous = (bool) $user->is_active;
        $next = (bool) $data['is_active'];

        $user->update(['is_active' => $next]);

        if ($previous !== $next) {
            UserStatusAudit::create([
                'user_id' => $user->id,
                'acted_by_user_id' => $request->user()->id,
                'action' => $next ? 'reactivated' : 'deactivated',
                'from_is_active' => $previous,
                'to_is_active' => $next,
            ]);
        }

        return redirect()
            ->route('admin.dashboard', $this->buildUserRedirectQuery($request))
            ->with('success', 'User status updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $user = User::query()->findOrFail($user->id);

        if ((int) $user->id === (int) $request->user()->id) {
            return redirect()
                ->route('admin.dashboard', $this->buildUserRedirectQuery($request))
                ->withErrors(['delete' => 'You cannot delete your own admin account.']);
        }

        $user->delete();

        UserStatusAudit::create([
            'user_id' => $user->id,
            'acted_by_user_id' => $request->user()->id,
            'action' => 'soft_deleted',
            'from_is_active' => $user->is_active,
            'to_is_active' => $user->is_active,
        ]);

        return redirect()
            ->route('admin.dashboard', $this->buildUserRedirectQuery($request))
            ->with('success', 'User deleted successfully. You can restore from deleted users.');
    }

    public function restore(Request $request, User $user): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($user->id);

        if (! $user->trashed()) {
            return redirect()
                ->route('admin.dashboard', $this->buildUserRedirectQuery($request))
                ->with('success', 'User is already active in the list.');
        }

        $user->restore();

        UserStatusAudit::create([
            'user_id' => $user->id,
            'acted_by_user_id' => $request->user()->id,
            'action' => 'restored',
            'from_is_active' => $user->is_active,
            'to_is_active' => $user->is_active,
        ]);

        return redirect()
            ->route('admin.dashboard', $this->buildUserRedirectQuery($request))
            ->with('success', 'User restored successfully.');
    }
}
