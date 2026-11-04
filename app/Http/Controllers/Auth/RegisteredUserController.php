<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AffiliateSettingsDefaults;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $isAffiliate = $request->input('account_type') === 'affiliate';

        $rules = [
            'account_type' => ['nullable', 'in:regular,affiliate'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'age' => ['required', 'integer', 'min:10', 'max:120'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($isAffiliate) {
            $rules['affiliate_code'] = ['nullable', 'string', 'alpha_num', 'min:4', 'max:20', 'unique:users,affiliate_code'];
            $rules['bank_name'] = ['required', 'string', 'max:60'];
            $rules['bank_account_number'] = ['required', 'string', 'max:60'];
            $rules['bank_account_holder'] = ['required', 'string', 'max:120'];
        }

        $request->validate($rules, [
            'affiliate_code.unique' => 'Kode afiliasi tersebut sudah digunakan mitra lain, silakan pilih kode lain atau kosongkan.',
            'affiliate_code.alpha_num' => 'Kode afiliasi hanya boleh berisi huruf dan angka tanpa spasi.',
            'bank_name.required' => 'Pilih Bank atau E-Wallet tujuan pencairan komisi.',
            'bank_account_number.required' => 'Nomor rekening / nomor E-Wallet wajib diisi untuk pencairan komisi.',
            'bank_account_holder.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        // Calculate estimated birth date from age
        $birthDate = now()->subYears((int) $request->age)->startOfYear()->toDateString();

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $birthDate,
            'password' => Hash::make($request->password),
            'is_affiliate' => $isAffiliate,
        ];

        if ($isAffiliate) {
            $affSettings = AffiliateSettingsDefaults::current();
            $userData['affiliate_code'] = User::generateUniqueAffiliateCode($request->name, $request->affiliate_code);
            $userData['affiliate_status'] = 'active';
            $userData['commission_rate'] = (float) ($affSettings['default_commission_rate'] ?? 10.00);
            $userData['bank_name'] = $request->bank_name;
            $userData['bank_account_number'] = $request->bank_account_number;
            $userData['bank_account_holder'] = $request->bank_account_holder;
            $userData['onboarding_completed'] = true;
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        if ($isAffiliate) {
            return redirect()->route('affiliate.index')->with('success', 'Selamat bergabung menjadi Mitra / Afiliator Resmi URSKYND! Kode Afiliasi Anda: ' . $user->affiliate_code);
        }

        return redirect(route('dashboard', absolute: false));
    }
}
