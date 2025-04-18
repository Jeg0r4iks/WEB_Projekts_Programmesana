<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
public function show(Request $request)
{
return Inertia::render('ProfileView', [
'user' => $request->user()
]);
}

public function update(Request $request)
{
$user = $request->user();
$user->update($request->only('username', 'email'));

return redirect()->back()->with('success', 'Profile updated!');
}
}
