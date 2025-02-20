<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AnggotaController extends Controller
{
    public function index(){
        return view ('anggota.index');
    }
    public function getAnggota(Request $request)
    {
        if ($request->ajax()) {
            $data = Anggota::select(['id', 'nama', 'NIK', 'alamat', 'no_telepon']);
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
        return view('anggota.add');
    }

    public function store(Request $request) {
        $request->validate([
            'nama' => 'required|string|max:255',
            'NIK' => 'required|string|unique:anggotas,NIK|min:16',
            'alamat' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:255|min:10',
        ]);
        $anggota = Anggota::create([
            'nama' => $request->nama,
            'NIK' => $request->NIK,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
        ]);
        return redirect('anggota')->with('success', 'Pengguna Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $anggota = Anggota::findOrFail($id);
        return response()->json($anggota);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'NIK' => 'required|string|min:16|unique:anggotas,NIK,' . $id,
            'alamat' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:255|min:10',
        ]);

        $anggota = Anggota::findOrFail($id);
        $anggota->update([
            'nama' => $request->nama,
            'NIK' => $request->NIK,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
        ]);

        return response()->json(['success' => 'Data anggota berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->delete();

        return response()->json(['success' => 'Anggota berhasil dihapus.']);
    }
}
