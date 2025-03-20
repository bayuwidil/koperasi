<?php

namespace App\Http\Controllers\pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $admins = User::where('role', 'admin')->get();
            return datatables()->of($admins)
                ->addColumn('action', function ($admin) {
                    return '
                        <a href="javascript:void(0)" data-id="' . $admin->id . '" class="btn btn-danger btn-sm deleteAdmin">Hapus</a>
                    ';
                })
                ->toJson();
        }

        return view('pimpinan.admin.index');
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:3|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'admin',
    ]);

    // Masukkan user_id ke dalam tabel anggotas
    Anggota::create([
        'user_id' => $user->id
    ]);

    return response()->json(['success' => 'Admin berhasil ditambahkan.']);
}

    public function edit($id)
{
    $admin = User::findOrFail($id);
    return response()->json($admin);
}

public function update(Request $request, $id)
{
    $admin = User::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'password' => 'nullable|string|min:3|confirmed', // Password opsional
    ]);

    // Update nama
    $admin->name = $request->name;

    // Jika ada password baru, update
    if ($request->filled('password')) {
        $admin->password = Hash::make($request->password);
    }

    $admin->save();

    return response()->json(['success' => 'Admin berhasil diperbarui.']);
}


    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => 'Admin berhasil dihapus.']);
    }
}
