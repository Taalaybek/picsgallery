<?php

namespace App\Http\Controllers;

use App\Models\User;

class SingleController extends Controller
{
	public function home()
	{
		return view('app');
	}
	
	/**
	 * Activation token
	 *
	 * @param string $token
	 */
	public function accountActivate(string $token)
	{
		$user = User::where('activation_token', $token)->first();

		if (!$user) {
			return abort(404, 'User with this token is not found.');
		}

		$user->update(['active' => true, 'activation_token' => '', 'email_verified_at' => now()]);

		return view('pages.account-activate', compact('user'));
	}
}
