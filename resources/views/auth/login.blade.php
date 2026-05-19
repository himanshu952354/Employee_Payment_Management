<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F4ECE6]">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PayFlow Enterprise</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="h-full flex flex-col justify-center relative overflow-hidden bg-[#F4ECE6] text-black px-6 py-12">

    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <!-- Logo -->
        <div class="flex items-center justify-center">
            <span class="text-4xl font-black uppercase tracking-tight text-black">PayFlow</span>
        </div>
        <h2 class="mt-6 text-center text-xl font-black uppercase tracking-wider text-black">
            Administration Portal
        </h2>
        <p class="mt-1.5 text-center text-xs text-slate-600 font-bold uppercase tracking-wider">
            Streamline employee payment transactions and payrolls.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <div class="bg-white border-2 border-black p-8 shadow-[8px_8px_0px_0px_#000] rounded-none">

            @if(session('error'))
                <div
                    class="mb-5 p-4 rounded-none bg-rose-50 border border-rose-250 text-rose-800 text-xs font-bold uppercase tracking-wider flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div
                    class="mb-5 p-4 rounded-none bg-emerald-50 border border-emerald-250 text-emerald-800 text-xs font-bold uppercase tracking-wider flex items-start gap-3">
                    <i class="fa-solid fa-circle-check mt-0.5 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form class="space-y-6" action="{{ url('/login') }}" method="POST">
                @csrf
                <div>
                    <label for="email"
                        class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Email
                        Address</label>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-black">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email', 'admin@payflow.com') }}"
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 pl-10 pr-3 text-black placeholder:text-slate-500 focus:ring-0 focus:border-black text-xs font-bold transition-all"
                            placeholder="admin@payflow.com">
                    </div>
                    @error('email')
                        <p
                            class="mt-2 text-[10px] text-rose-650 flex items-center gap-1.5 font-bold uppercase tracking-wider">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password"
                            class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Password</label>
                    </div>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-black">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input id="password" name="password" type="password" required value="password"
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 pl-10 pr-3 text-black placeholder:text-slate-500 focus:ring-0 focus:border-black text-xs font-bold transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" checked
                            class="h-4 w-4 rounded-none border-black bg-[#F4ECE6] text-black focus:ring-0">
                        <label for="remember"
                            class="ml-2.5 block text-xs text-slate-600 font-extrabold uppercase tracking-wider">Remember
                            session</label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-none bg-black hover:bg-neutral-800 py-3.5 px-4 text-xs font-extrabold uppercase tracking-wider text-white border border-black shadow-[3px_3px_0px_0px_#000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all">
                        Sign in to account <i class="fa-solid fa-arrow-right ml-2 mt-0.5"></i>
                    </button>
                </div>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-8 pt-6 border-t border-black/10">
                <div class="bg-[#F4ECE6] border border-black rounded-none p-4 shadow-[3px_3px_0px_0px_#000]">
                    <h4
                        class="text-[9px] font-black uppercase tracking-wider text-black mb-2.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-shield"></i> Demo Administrator Account
                    </h4>
                    <div
                        class="flex flex-col gap-1.5 text-xs text-slate-650 font-bold uppercase tracking-wider text-[9px]">
                        <div class="flex justify-between">
                            <span>Email:</span>
                            <span class="font-mono text-black font-extrabold">admin@payflow.com</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Password:</span>
                            <span class="font-mono text-black font-extrabold">password</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>