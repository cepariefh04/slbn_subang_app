<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.index', ['users' => $users]);
    }

    public function tambahPengguna(Request $request)
    {
        try {
            $validasiData = $request->validate([
                'name' => 'required|max:55',
                'email' => 'email|required|unique:users',
                'password' => 'required',
                'level' => 'required'
            ]);
            $validasiData['password'] = bcrypt($request->password);
            User::create($validasiData);

            return redirect()->route('admin.dashboard')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Terjadi kesalahan saat menambahkan pengguna. Silakan coba lagi.');
        }
    }

    public function delete($id)
    {
        $item = User::find($id);
        if (!$item) {
            return redirect()->route('admin.dashboard')->with('error', 'Item not found.');
        }

        $item->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Item deleted successfully.');
    }
}
