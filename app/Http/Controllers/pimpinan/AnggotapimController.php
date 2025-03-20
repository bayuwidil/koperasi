<?php

namespace App\Http\Controllers\pimpinan;
use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AnggotapimController extends Controller
{
    public function index(){
        return view ('pimpinan.anggota.index');
    }

    public function getAnggota(Request $request)
    {
        if ($request->ajax()) {
            $data = Anggota::with('user')->select(['id', 'user_id', 'nama', 'NIK','email', 'alamat', 'no_telepon']);
            return DataTables::of($data)
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-success btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } 
    }

    public function add()
    {
        return view('pimpinan.anggota.add');
    }

    public function store(Request $request) {
        $request->validate([
            'nama' => 'required|string|max:255',
            'NIK' => 'required|string|unique:anggotas,NIK|min:16',
            'email' => 'required|string|email|unique:users,email',
            'alamat' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:255|min:10',
        ]);

        DB::transaction(function () use ($request) {
            // Buat user baru
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make('123'), // Default password
            ]);

            // Buat anggota baru dengan user_id
            Anggota::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'NIK' => $request->NIK,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'no_telepon' => $request->no_telepon,
            ]);
        });

        return redirect('anggota')->with('success', 'Pengguna Berhasil Ditambahkan');
    }
}
