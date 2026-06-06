<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <style>
        .login-input-wrapper {
            position: relative;
            width: 100%;
        }
        .login-input {
            width: 100%;
            padding-top: 12px;
            padding-bottom: 12px;
            padding-left: 42px;
            padding-right: 42px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            background-color: rgba(248, 250, 252, 0.8);
            transition: all 150ms ease-in-out;
            box-sizing: border-box;
        }
        .login-input:focus {
            outline: none;
            border-color: #1e3a8a; /* primary color */
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
        }
        .login-icon-left {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .login-icon-right {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            color: #94a3b8;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            width: 20px;
            height: 20px;
        }
        .login-icon-right:hover {
            color: #475569;
        }
        .login-submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #1e3a8a; /* primary */
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            border-radius: 12px;
            transition: all 150ms ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(30, 58, 138, 0.1), 0 2px 4px -1px rgba(30, 58, 138, 0.06);
            box-sizing: border-box;
        }
        .login-submit-btn:hover {
            background-color: #1e40af; /* primary hover */
            box-shadow: 0 10px 15px -3px rgba(30, 58, 138, 0.2), 0 4px 6px -2px rgba(30, 58, 138, 0.05);
        }
        .login-submit-btn:active {
            transform: scale(0.98);
        }
    </style>

    <div class="mb-6">
        <h2 class="text-xl font-black text-slate-800 text-center tracking-tight">Sign In</h2>
        <p class="text-xs text-slate-400 font-bold text-center mt-1">Please enter your credentials to access the portal</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email or Phone -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email or Phone</label>
            <div class="login-input-wrapper">
                <div class="login-icon-left">
                    <i class="fa-solid fa-user"></i>
                </div>
                <input id="email" 
                       class="login-input" 
                       type="text" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       placeholder="Enter Email or Phone Number"
                       autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div x-data="{ showPassword: false }">
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Password</label>
                @if (Route::has('password.request'))
                    <!-- <a class="text-xs font-extrabold text-secondary hover:text-secondary-hover transition duration-150" href="{{ route('password.request') }}">
                        Forgot password?
                    </a> -->
                @endif
            </div>
            <div class="login-input-wrapper">
                <div class="login-icon-left">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <input id="password" 
                       :type="showPassword ? 'text' : 'password'" 
                       name="password" 
                       required 
                       placeholder="••••••••"
                       class="login-input"
                       autocomplete="current-password" />
                <button type="button" 
                        @click="showPassword = !showPassword" 
                        class="login-icon-right"
                        title="Toggle password visibility">
                    <i class="fa-solid text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-255 text-primary focus:ring-primary/20 shadow-sm cursor-pointer" name="remember">
            <label for="remember_me" class="ms-2 text-xs font-bold text-slate-500 cursor-pointer select-none">Remember my session</label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="login-submit-btn">
                <i class="fa-solid fa-right-to-bracket"></i>
                Log In to Account
            </button>
        </div>
    </form>
</x-guest-layout>
