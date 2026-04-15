<?php

namespace App\Http\Controllers;

use App\Http\Requests\TelescopeLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TelescopeAuthController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! config('telescope.enabled', true)) {
            abort(404);
        }

        if ($request->user() && Gate::allows('viewTelescope', $request->user())) {
            return redirect()->to('/'.ltrim(config('telescope.path', 'telescope'), '/'));
        }

        return view('telescope.auth-login');
    }

    /**
     * Сессионный вход под тем же guard `web`, что использует Telescope.
     *
     * @throws ValidationException
     */
    public function store(TelescopeLoginRequest $request): RedirectResponse
    {
        if (! config('telescope.enabled', true)) {
            abort(404);
        }

        $remember = $request->boolean('remember');

        if (! Auth::attempt([
            'username' => $request->validated('username'),
            'password' => $request->validated('password'),
        ], $remember)) {
            throw ValidationException::withMessages([
                'username' => [__('auth.failed')],
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user === null || ! Gate::allows('viewTelescope', $user)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'username' => ['Нет доступа к Telescope для этой учётной записи.'],
            ]);
        }

        return redirect()->intended('/'.ltrim(config('telescope.path', 'telescope'), '/'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('telescope-auth.login');
    }
}
