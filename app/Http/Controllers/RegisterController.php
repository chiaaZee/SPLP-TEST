<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Traits\RecaptchaValidation;

class RegisterController extends Controller
{
    use RecaptchaValidation;

    public function showRegistrationForm()
    {
        $pageConfigs = ['myLayout' => 'blank'];
        $agencies = Agency::where('status', 'active')->get();
        return view('content.authentications.auth-register-cover', ['pageConfigs' => $pageConfigs, 'agencies' => $agencies]);
    }

    public function register(Request $request)
    {
        $this->validateRecaptcha($request->input('g-recaptcha-response'));

        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'is_agency' => 'nullable|boolean',
            // If is_agency is checked, we expect agency_id. If agency_id is 'other', we need agency_name/code
            'agency_id' => 'nullable|required_if:is_agency,1',
            'agency_name' => 'nullable|required_if:agency_id,other|string|max:255',
            'agency_code' => 'nullable|required_if:agency_id,other|string|max:255|unique:agencies,code',
        ]);

        $agencyId = null;
        $role = 'user';
        $status = 'pending'; // Default status for new registrations

        if ($request->has('is_agency') && $request->is_agency) {
            $role = 'dinas';

            if ($request->agency_id === 'other') {
                // Create new pending agency
                $agency = Agency::create([
                    'name' => $request->agency_name,
                    'code' => $request->agency_code,
                    'email' => $request->email,
                    'status' => 'pending',
                ]);
                $agencyId = $agency->id;
            } else {
                // Use existing active agency
                $agencyId = $request->agency_id;
            }
        }

        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $role,
            'agency_id' => $agencyId,
            'status' => $status,
        ]);

        // Login the user automatically so they can see the pending status page correctly
        Auth::login($user);

        return redirect()->route('pages-pending-approval')->with('success', 'Registrasi berhasil! Silakan tunggu verifikasi admin.');
    }
}
