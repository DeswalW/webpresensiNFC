<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::orderBy('name')->paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'username' => 'required|string|max:255|unique:admins,username',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,admin,wali_kelas',
            'assigned_class' => 'nullable|string|max:50',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'assigned_class' => $request->assigned_class,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    public function show(Admin $admin)
    {
        return view('admin.admins.show', compact('admin'));
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('admins')->ignore($admin->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('admins')->ignore($admin->id)],
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:super_admin,admin,wali_kelas',
            'assigned_class' => 'nullable|string|max:50',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'role' => $request->role,
            'assigned_class' => $request->assigned_class,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin berhasil diperbarui.');
    }

    public function destroy(Admin $admin)
    {
        // Jangan hapus admin yang sedang login
        if ($admin->id === auth()->guard('admin')->id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Tidak dapat menghapus akun yang sedang aktif.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin berhasil dihapus.');
    }

    public function toggleStatus(Admin $admin)
    {
        // Jangan nonaktifkan admin yang sedang login
        if ($admin->id === auth()->guard('admin')->id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Tidak dapat menonaktifkan akun yang sedang aktif.');
        }

        $admin->update(['is_active' => !$admin->is_active]);

        $status = $admin->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.admins.index')
            ->with('success', "Admin berhasil {$status}.");
    }
}


