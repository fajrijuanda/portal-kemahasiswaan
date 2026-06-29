<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));
        $roleFilter = trim((string) $request->query('role', ''));

        $usersQuery = User::with(['prodi', 'roles'])
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->when($roleFilter !== '', fn ($query) => $query->role($roleFilter))
            ->orderBy('name');

        $roles = Role::orderBy('name')->pluck('name');
        $roleCounts = $roles->mapWithKeys(fn ($role) => [$role => User::role($role)->count()]);

        return view('users.index', [
            'users' => $usersQuery->paginate(request('limit', 10))->withQueryString(),
            'prodis' => Prodi::orderBy('nama')->get(),
            'roles' => $roles,
            'roleCounts' => $roleCounts,
            'totalUsers' => User::count(),
            'keyword' => $keyword,
            'roleFilter' => $roleFilter,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $role = $data['role'];
        unset($data['role']);
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->syncRoles([$role]);

        return back()->with('status', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, false, $user->id);
        $role = $data['role'];
        unset($data['role']);
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $user->syncRoles([$role]);

        return back()->with('status', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if(\Illuminate\Support\Facades\Auth::id() === $user->id, 422, 'Tidak bisa menghapus user yang sedang login.');
        $user->delete();

        return back()->with('status', 'User berhasil dihapus.');
    }

    private function validated(Request $request, bool $passwordRequired, ?int $userId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.($userId ?: 'NULL').',id'],
            'password' => [$passwordRequired ? 'required' : 'nullable', 'string', 'min:8'],
            'prodi_id' => ['nullable', 'exists:prodis,id'],
            'role' => ['required', 'exists:roles,name'],
        ]);
    }
}