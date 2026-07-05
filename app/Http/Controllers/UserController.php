<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('store')->latest()->paginate(10);
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        return view('kelola-user.index', compact('users', 'stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['admin_master', 'pemilik', 'kasir'])],
            'store_id' => ['nullable', 'exists:stores,id'],
        ], [
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // store_id required untuk role kasir
        if ($validated['role'] === 'kasir' && empty($request->store_id)) {
            return redirect()->back()->with('error', 'Store ID wajib diisi untuk role Kasir.')->withInput();
        }

        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'store_id' => $validated['role'] === 'kasir' ? $request->store_id : null,
                'email_verified_at' => now(), 
            ]);

            return redirect()->back()->with('success', 'User berhasil ditambahkan!');
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan user. Silakan coba lagi.');
        }
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['admin_master', 'pemilik', 'kasir'])],
            'store_id' => ['nullable', 'exists:stores,id'],
        ], [
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // store_id required untuk role kasir
        if ($validated['role'] === 'kasir' && empty($request->store_id)) {
            return redirect()->back()->with('error', 'Store ID wajib diisi untuk role Kasir.')->withInput();
        }

        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'store_id' => $validated['role'] === 'kasir' ? $request->store_id : null,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            return redirect()->back()->with('success', 'User berhasil diperbarui!');
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui user. Silakan coba lagi.');
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        try {
            $user->delete();
            return redirect()->back()->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus user. Silakan coba lagi.');
        }
    }
}
