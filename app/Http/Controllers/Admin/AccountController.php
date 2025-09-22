<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        return view('admin.accounts.index', [
            'accounts' => Account::orderBy('nama_akun_belanja', 'asc')->get()
        ]);
    }

    public function create()
    {
        return view('admin.accounts.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_akun_belanja' => 'required|string|max:255|unique:accounts'
        ]);
        Account::create($validated);
        return redirect()->route('accounts.index')->with('success', 'Akun Belanja berhasil ditambahkan.');
    }

    public function show(Account $account)
    {
        // Not used in this workflow
    }

    public function edit(Account $account)
    {
        return view('admin.accounts.form', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'nama_akun_belanja' => 'required|string|max:255|unique:accounts,nama_akun_belanja,'.$account->id
        ]);
        $account->update($validated);
        return redirect()->route('accounts.index')->with('success', 'Akun Belanja berhasil diperbarui.');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Akun Belanja berhasil dihapus.');
    }
}