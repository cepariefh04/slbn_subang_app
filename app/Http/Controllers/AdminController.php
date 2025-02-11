<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        $storagePath = config('app.storage_path');
        return view('admin.index', ['users' => $users, 'storagePath' => $storagePath]);
    }

    public function tambahPengguna(Request $request)
    {
        try {
            $validasiData = $request->validate([
                'name' => 'required|max:55',
                'email' => 'email|required|unique:users',
                'password' => 'required',
                'level' => 'required',
                'photo' => 'nullable|image'
            ]);
            $validasiData['password'] = bcrypt($request->password);

            if ($request->hasFile('photo')) {
                $validasiData['photo'] = $request->file('photo')->store('photos', 'public');
            }

            User::create($validasiData);

            return redirect()->route('admin.dashboard')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Terjadi kesalahan saat menambahkan pengguna. Silakan coba lagi.');
        }
    }

    public function updatePengguna(Request $request, $id)
    {

        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|min:6',
                'level' => 'required|in:Admin,SarPras,TU',
                'photo' => 'nullable|image|max:2048',
            ]);

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->password) {
                $user->password = bcrypt($request->password);
            }

            if ($request->hasFile('photo')) {
                $user->photo = $request->file('photo')->store('photos', 'public');
            }

            $user->level = $request->level;
            $user->save();

            return redirect()->back()->with('success', 'Pengguna berhasil diperbarui!');

            // return redirect()->route('admin.dashboard')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan pengguna. Silakan coba lagi.');
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
