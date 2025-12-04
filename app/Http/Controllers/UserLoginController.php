<?php

namespace App\Http\Controllers;

use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class UserLoginController extends Controller
{
    //Listen
    public function index()
    {
        $userlogins = UserLogin::with('user')->orderBy('id', 'desc')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User Logins retrieved successfully.',
                'data' => $userlogins,
            ]);
        }

        $breadcrumbs = [
            ['label' => 'User Logins', 'url' => null],
        ];

        $nav_bar = 'userlogin';
        $pageTitle = 'User Logins';

        return view('admin.userlogins.list', compact('userlogins', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }


    //Create
    public function create()
    {
        $pageTitle = "ADD User Login";
        $nav_bar = "userlogin";
        $breadcrumbs = [
            ['label' => 'UserLogins', 'url' => route('admin.userlogin.index')],
            ['label' => 'Add User Login', 'url' => null],
        ];

        return view('admin.userlogins.form', [
            'action' => 'create',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'userLogin' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    //Store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'nullable|boolean',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        UserLogin::create($validated);

        return redirect()->route('admin.userlogin.index')
            ->with('success', 'User Login created successfully.');
    }

    //Edit
    public function show($id)
    {
        $UserLogin= UserLogin::findOrFail($id);

        $pageTitle = "Edit User Login";
        $nav_bar = "userlogin";
        $breadcrumbs = [
            ['label' => 'UserLogins', 'url' => route('admin.userlogin.index')],
            ['label' => 'Edit User Login', 'url' => null],
        ];

        return view('admin.userlogins.form', [
            'action' => 'edit',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'userLogin' => $UserLogin,
        ]);
    }

    //Update
    public function update(Request $request, $id)
    {
        $UserLogin= UserLogin::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'nullable|boolean',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $UserLogin->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $request->body,
            'status' => $request->status,
            'description' => $request->description,
            'created_at' => $request->created_at,
        ]);

        return redirect()->route('admin.userlogin.index')
            ->with('success', 'User Login updated successfully.');
    }

    //Delete
    public function destroy($id)
    {
        $UserLogin= UserLogin::find($id);

        if (!$UserLogin) {
            return redirect()->route('admin.userlogin.index')
                ->with('error', 'User Login not found.');
        }

        $UserLogin->delete();

        return redirect()->route('admin.userlogin.index')
            ->with('success', 'User Login deleted successfully.');
    }

}
