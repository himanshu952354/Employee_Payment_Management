<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F4ECE6]">

<head>
    <!-- Dark Mode Immediate Bootstrapper -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PayFlow - ' . (Auth::user()->company_name ?? 'Workspace'))</title>
    <!-- Chart.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Lenis Smooth Scroll Engine -->
    <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>

    <!-- Outfit Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        /* Custom scrollbar matching brutalist look */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #F4ECE6;
            border-left: 1px solid #000000;
        }

        ::-webkit-scrollbar-thumb {
            background: #000000;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #2D3748;
        }

        /* ----------------------------------------------------
           DARK MODE STYLESHEET OVERRIDES
           ---------------------------------------------------- */
        [data-theme="dark"] {
            background-color: #121212 !important;
            color: #f8fafc !important;
        }

        [data-theme="dark"] body {
            background-color: #121212 !important;
            color: #f8fafc !important;
        }

        /* Overrides for dynamic container boxes */
        [data-theme="dark"] .bg-\[\#F4ECE6\],
        [data-theme="dark"] header.bg-\[\#F4ECE6\],
        [data-theme="dark"] aside.bg-\[\#F4ECE6\],
        [data-theme="dark"] div.bg-\[\#F4ECE6\] {
            background-color: #121212 !important;
        }

        [data-theme="dark"] .bg-white,
        [data-theme="dark"] .bg-\[\#FCFAF7\],
        [data-theme="dark"] .bg-slate-50,
        [data-theme="dark"] .bg-emerald-50,
        [data-theme="dark"] .bg-amber-50,
        [data-theme="dark"] .bg-red-50,
        [data-theme="dark"] .bg-yellow-50 {
            background-color: #1e1e1e !important;
            color: #f8fafc !important;
        }

        /* Input overrides */
        [data-theme="dark"] input,
        [data-theme="dark"] select,
        [data-theme="dark"] textarea {
            background-color: #2d2d2d !important;
            color: #ffffff !important;
            border-color: #f8fafc !important;
        }

        /* Border overrides */
        [data-theme="dark"] .border-black,
        [data-theme="dark"] .border-2.border-black,
        [data-theme="dark"] .border-t-2.border-black,
        [data-theme="dark"] .border-b-2.border-black,
        [data-theme="dark"] .border-l-2.border-black,
        [data-theme="dark"] .border-r-2.border-black {
            border-color: #f8fafc !important;
        }

        [data-theme="dark"] .border-transparent {
            border-color: transparent !important;
        }

        /* Text color overrides */
        [data-theme="dark"] .text-black,
        [data-theme="dark"] .text-slate-800,
        [data-theme="dark"] .text-slate-700,
        [data-theme="dark"] .text-slate-900,
        [data-theme="dark"] .text-neutral-900 {
            color: #f8fafc !important;
        }

        [data-theme="dark"] .text-slate-500,
        [data-theme="dark"] .text-slate-455 {
            color: #94a3b8 !important;
        }

        /* Hover and active overrides */
        [data-theme="dark"] .hover\:bg-black\/5:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
        }

        [data-theme="dark"] .hover\:text-rose-600:hover {
            color: #f43f5e !important;
        }

        /* Shadow overrides */
        [data-theme="dark"] [class*="shadow-["] {
            box-shadow: 3px 3px 0px 0px #ffffff !important;
        }

        [data-theme="dark"] [class*="shadow-[8px_8px_"] {
            box-shadow: 6px 6px 0px 0px #ffffff !important;
        }

        [data-theme="dark"] [class*="shadow-[6px_6px_"] {
            box-shadow: 4px 4px 0px 0px #ffffff !important;
        }

        [data-theme="dark"] [class*="shadow-[4px_4px_"] {
            box-shadow: 3px 3px 0px 0px #ffffff !important;
        }

        [data-theme="dark"] [class*="shadow-[2px_2px_"] {
            box-shadow: 2px 2px 0px 0px #ffffff !important;
        }

        /* Interactive buttons */
        [data-theme="dark"] .bg-black {
            background-color: #ffffff !important;
            color: #121212 !important;
            border-color: #ffffff !important;
        }

        [data-theme="dark"] .text-white {
            color: #121212 !important;
        }

        [data-theme="dark"] button.bg-black,
        [data-theme="dark"] a.bg-black {
            background-color: #ffffff !important;
            color: #121212 !important;
        }

        /* Autocomplete dropdown cards */
        [data-theme="dark"] #global-search-dropdown,
        [data-theme="dark"] #notifications-dropdown {
            background-color: #1e1e1e !important;
            border-color: #ffffff !important;
        }

        [data-theme="dark"] .divide-y.divide-black\/10> :not([hidden])~ :not([hidden]) {
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        /* Sidebar overrides */
        [data-theme="dark"] aside {
            background-color: #1e1e1e !important;
            border-right: 1px solid #ffffff !important;
        }

        [data-theme="dark"] aside .p-4.border-t.border-black.bg-\[\#F4ECE6\],
        [data-theme="dark"] aside div.bg-\[\#F4ECE6\] {
            background-color: #121212 !important;
            border-top: 1px solid #ffffff !important;
        }
    </style>
    @yield('styles')
</head>

<body class="h-full text-black flex overflow-hidden bg-[#F4ECE6] antialiased">

    <!-- 1. Left Sidebar Navigation -->
    <aside class="hidden lg:flex lg:flex-col lg:w-72 lg:flex-shrink-0 bg-[#F4ECE6] border-r border-black z-20 relative">
        <!-- Logo block -->
        <div class="h-20 flex items-center gap-2.5 px-6 border-b border-black select-none">
            <span class="text-2xl font-black tracking-tight text-black">PayFlow</span>
            <span
                class="text-[8px] px-2 py-0.5 rounded-none font-black bg-black text-white border border-black uppercase tracking-widest truncate max-w-[130px]"
                title="{{ Auth::user()->company_name ?? 'Workspace' }}">
                {{ Auth::user()->company_name ?? 'Workspace' }}
            </span>
        </div>

        <!-- Sidebar Navigation Menu -->
        @php
            $unreadPrivateCount = \App\Models\Message::where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->where('is_broadcast', false)
                ->count();

            $totalCompanyBroadcasts = \App\Models\Message::where('is_broadcast', true)
                ->where('sender_id', '!=', Auth::id())
                ->whereHas('sender', function ($query) {
                    $query->where('company_name', Auth::user()->company_name);
                })
                ->count();

            $unreadChatCount = $unreadPrivateCount; // legacy fallback
        @endphp
        <nav class="flex-1 space-y-2.5 px-4 py-6 overflow-y-auto">
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-none text-xs font-bold uppercase tracking-wider transition-all {{ Request::is('/') || Request::is('dashboard*') ? 'bg-black text-white border border-black shadow-[3px_3px_0px_0px_#000]' : 'text-slate-800 hover:bg-black/5 border border-transparent' }}">
                    <i class="fa-solid fa-chart-pie text-base"></i>
                    <span>Executive Dashboard</span>
                </a>

                <a href="{{ route('employees.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-none text-xs font-bold uppercase tracking-wider transition-all {{ Request::is('employees*') ? 'bg-black text-white border border-black shadow-[3px_3px_0px_0px_#000]' : 'text-slate-800 hover:bg-black/5 border border-transparent' }}">
                    <i class="fa-solid fa-users text-base"></i>
                    <span>Employee Directory</span>
                </a>

                <a href="{{ route('attendance.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-none text-xs font-bold uppercase tracking-wider transition-all {{ Request::is('attendance*') ? 'bg-black text-white border border-black shadow-[3px_3px_0px_0px_#000]' : 'text-slate-800 hover:bg-black/5 border border-transparent' }}">
                    <i class="fa-solid fa-calendar-check text-base"></i>
                    <span>Daily Attendance</span>
                </a>

                <a href="{{ route('payroll.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-none text-xs font-bold uppercase tracking-wider transition-all {{ Request::is('payroll*') ? 'bg-black text-white border border-black shadow-[3px_3px_0px_0px_#000]' : 'text-slate-800 hover:bg-black/5 border border-transparent' }}">
                    <i class="fa-solid fa-wallet text-base"></i>
                    <span>Payroll & Payouts</span>
                </a>

                <a href="{{ route('chat.index') }}"
                    class="flex items-center justify-between px-4 py-3 rounded-none text-xs font-bold uppercase tracking-wider transition-all {{ Request::is('chat*') ? 'bg-black text-white border border-black shadow-[3px_3px_0px_0px_#000]' : 'text-slate-800 hover:bg-black/5 border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-bullhorn text-base"></i>
                        <span>Company Broadcast</span>
                    </div>
                    @if($unreadChatCount > 0)
                        <span
                            class="px-2 py-0.5 text-[9px] font-black bg-rose-500 text-white border border-black animate-pulse">
                            {{ $unreadChatCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('settings.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-none text-xs font-bold uppercase tracking-wider transition-all {{ Request::is('settings*') ? 'bg-black text-white border border-black shadow-[3px_3px_0px_0px_#000]' : 'text-slate-800 hover:bg-black/5 border border-transparent' }}">
                    <i class="fa-solid fa-gears text-base"></i>
                    <span>Workspace Settings</span>
                </a>
            @else
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-none text-xs font-bold uppercase tracking-wider transition-all {{ Request::is('/') || Request::is('dashboard*') ? 'bg-black text-white border border-black shadow-[3px_3px_0px_0px_#000]' : 'text-slate-800 hover:bg-black/5 border border-transparent' }}">
                    <i class="fa-solid fa-id-card text-base"></i>
                    <span>My Personal Dossier</span>
                </a>

                <a href="{{ route('chat.index') }}"
                    class="flex items-center justify-between px-4 py-3 rounded-none text-xs font-bold uppercase tracking-wider transition-all {{ Request::is('chat*') ? 'bg-black text-white border border-black shadow-[3px_3px_0px_0px_#000]' : 'text-slate-800 hover:bg-black/5 border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-bullhorn text-base"></i>
                        <span>Company Broadcast</span>
                    </div>
                    @if($unreadChatCount > 0)
                        <span
                            class="px-2 py-0.5 text-[9px] font-black bg-rose-500 text-white border border-black animate-pulse">
                            {{ $unreadChatCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('settings.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-none text-xs font-bold uppercase tracking-wider transition-all {{ Request::is('settings*') ? 'bg-black text-white border border-black shadow-[3px_3px_0px_0px_#000]' : 'text-slate-800 hover:bg-black/5 border border-transparent' }}">
                    <i class="fa-solid fa-user-gear text-base"></i>
                    <span>Account Settings</span>
                </a>
            @endif
        </nav>

        <!-- Sidebar Footer & Admin Info -->
        <div class="p-4 border-t border-black bg-[#F4ECE6]">
            <div
                class="flex items-center gap-3 p-2 rounded-none bg-white border border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,0.15)]">
                <div
                    class="w-10 h-10 rounded-none bg-black flex items-center justify-center font-extrabold text-white text-sm border border-black shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-black truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                    <p class="text-[10px] text-slate-500 font-semibold truncate">{{ Auth::user()->email ??
                        'admin@payflow.com' }}</p>
                </div>
                <!-- Logout Trigger -->
                <form action="{{ route('logout') }}" method="POST" class="inline" id="logout-form">
                    @csrf
                    <button type="submit" class="p-2 text-black hover:text-rose-600 transition-colors duration-150"
                        title="Sign Out">
                        <i class="fa-solid fa-arrow-right-from-bracket text-base"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- 2. Main Content Area Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative z-10">

        <!-- Header / Top Bar -->
        <header class="h-20 flex items-center justify-between px-6 bg-[#F4ECE6] border-b border-black">
            <!-- Mobile Menu Toggle Button -->
            <button
                class="lg:hidden p-2 text-black hover:bg-black/5 border border-transparent rounded-none transition-colors duration-150">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>

            <!-- Interactive Global Search Container -->
            <div class="hidden md:block relative" id="global-search-container">
                <div
                    class="flex items-center gap-2.5 px-4 py-2 rounded-none bg-white border border-black w-80 text-black shadow-[2px_2px_0px_0px_#000] focus-within:shadow-[3px_3px_0px_0px_#000] transition-all">
                    <i class="fa-solid fa-magnifying-glass text-slate-500"></i>
                    <input type="text" id="global-search-input"
                        placeholder="Global search (e.g. employee, announcement)..." autocomplete="off"
                        class="bg-transparent border-0 outline-none text-xs w-full text-black placeholder:text-slate-455 font-semibold">
                    <span id="global-search-spinner" class="hidden text-slate-400"><i
                            class="fa-solid fa-spinner animate-spin text-[10px]"></i></span>
                </div>

                <!-- Floating Autocomplete Dropdown Card -->
                <div id="global-search-dropdown"
                    class="hidden absolute top-full left-0 mt-2 w-96 bg-white border-2 border-black shadow-[4px_4px_0px_0px_#000] z-50 overflow-hidden flex flex-col max-h-[30rem]">
                    <div id="global-search-results" class="overflow-y-auto divide-y divide-black/10">
                        <!-- Dynamic Results Injected Here -->
                    </div>
                </div>
            </div>

            <!-- Header Operations Icons -->
            <div class="flex items-center gap-4">
                <!-- Dark/Light Theme Toggle Switch -->
                <button id="theme-toggle"
                    class="p-2 text-black hover:bg-black/5 border border-transparent rounded-none transition-all duration-150 flex items-center justify-center cursor-pointer focus:outline-none"
                    title="Toggle Dark/Light Mode">
                    <i id="theme-toggle-icon" class="fa-solid fa-moon text-lg"></i>
                </button>

                <!-- Active Notifications Dropdown Trigger -->
                <div class="relative flex items-center justify-center" id="notifications-wrapper">
                    <button onclick="toggleNotificationsDropdown()"
                        class="p-2 text-black hover:bg-black/5 border border-transparent rounded-none transition-all duration-150 relative flex items-center justify-center cursor-pointer focus:outline-none"
                        title="Unread Messages">
                        <i class="fa-regular fa-bell text-lg"></i>
                        <!-- Dynamic notification dot managed by JS -->
                        <span id="bell-ping"
                            class="hidden absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 border border-black inline-block rounded-none animate-ping"></span>
                        <span id="bell-dot"
                            class="hidden absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 border border-black inline-block rounded-none"></span>
                    </button>

                    <!-- Neo-Brutalist Dropdown Box -->
                    <div id="notifications-dropdown"
                        class="hidden absolute right-0 top-full mt-2.5 w-72 bg-white border-2 border-black shadow-[4px_4px_0px_0px_#000] z-50 overflow-hidden flex flex-col">
                        <!-- Dropdown Header -->
                        <div
                            class="p-3 border-b-2 border-black bg-[#F4ECE6] flex items-center justify-between select-none">
                            <span class="text-[10px] font-black uppercase tracking-wider text-black">
                                <i class="fa-solid fa-bell text-black mr-1 animate-pulse"></i> Notifications
                            </span>
                            <span id="dropdown-badge"
                                class="hidden px-2 py-0.5 text-[8px] font-black uppercase bg-rose-500 text-white border border-black animate-pulse">
                                0 New
                            </span>
                        </div>

                        <!-- Dropdown Body / Notifications List -->
                        <div id="notifications-list"
                            class="max-h-60 overflow-y-auto divide-y divide-black/10 border-b border-black">
                            @php
                                $unreadMessages = \App\Models\Message::where('is_broadcast', true)
                                    ->where('sender_id', '!=', Auth::id())
                                    ->whereHas('sender', function ($query) {
                                        $query->where('company_name', Auth::user()->company_name);
                                    })
                                    ->with('sender')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp

                            @foreach($unreadMessages as $msg)
                                <a href="{{ route('chat.index') }}"
                                    class="block p-3 hover:bg-neutral-50 transition-colors notification-item">
                                    <div class="flex gap-2.5 min-w-0">
                                        <div
                                            class="w-6.5 h-6.5 flex-shrink-0 flex items-center justify-center font-black text-[9px] uppercase border bg-black text-white border-black">
                                            {{ substr($msg->sender->name, 0, 2) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[10px] font-black text-black leading-none truncate">
                                                {{ $msg->sender->name }}
                                                ({{ $msg->sender->role === 'admin' ? 'Admin' : 'Employee' }})
                                            </p>
                                            <p class="text-[9px] font-semibold text-slate-700 truncate mt-1">
                                                {{ $msg->message }}
                                            </p>
                                            <span
                                                class="text-[8px] font-bold text-slate-400 block mt-1 uppercase tracking-wider">
                                                {{ $msg->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                            <div id="notifications-empty-state"
                                class="{{ count($unreadMessages) > 0 ? 'hidden' : '' }} p-6 text-center text-xs text-slate-500 font-bold uppercase tracking-wider bg-white select-none">
                                <i
                                    class="fa-solid fa-circle-check text-emerald-500 text-2xl mb-2 block animate-pulse"></i>
                                All Caught Up!
                            </div>
                        </div>

                        <!-- Dropdown Footer -->
                        <a href="{{ route('chat.index') }}"
                            class="block text-center py-3 bg-black hover:bg-neutral-900 text-white font-black text-[9px] uppercase tracking-widest transition-all">
                            Open Company Broadcasts
                        </a>
                    </div>
                </div>

                <div class="h-6 w-px bg-black/30"></div>

                <!-- User Profile & Dynamic Role (Clickable) -->
                <button onclick="openProfileModal()"
                    class="flex items-center gap-2.5 bg-white hover:bg-[#F4ECE6] border border-black px-3.5 py-1.5 rounded-none shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[1px_1px_0px_0px_#000] transition-all select-none cursor-pointer text-left">
                    <div
                        class="w-6 h-6 rounded-none bg-black text-white border border-black flex items-center justify-center font-black text-[10px] uppercase shadow-sm">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span
                            class="text-[9px] font-black uppercase tracking-wider text-black truncate max-w-[100px] leading-tight"
                            title="{{ Auth::user()->name ?? 'User' }}">
                            {{ Auth::user()->name ?? 'User' }}
                        </span>
                        <span class="text-[7px] font-bold uppercase tracking-widest text-slate-500 leading-none mt-0.5">
                            {{ Auth::user()->role ?? 'Member' }}
                        </span>
                    </div>
                </button>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8">
            <div class="lenis-content">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- ================= DYNAMIC USER PROFILE MODAL ================= -->
    <div id="profile-modal" onclick="if(event.target === this) closeProfileModal()"
        class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div id="profile-modal-card"
            class="bg-[#F4ECE6] border-2 border-black p-8 rounded-none shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-md transform scale-95 transition-all duration-150 relative">

            <!-- Close button -->
            <button onclick="closeProfileModal()"
                class="absolute top-4 right-4 w-7 h-7 bg-white hover:bg-rose-50 text-black hover:text-rose-600 border border-black flex items-center justify-center rounded-none shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none transition-all cursor-pointer">
                <i class="fa-solid fa-xmark text-sm font-bold"></i>
            </button>

            <!-- Card Header -->
            <div class="text-center space-y-4">
                <div
                    class="w-16 h-16 rounded-none bg-black text-white border-2 border-black flex items-center justify-center font-black text-2xl uppercase mx-auto shadow-[4px_4px_0px_0px_rgba(0,0,0,0.15)]">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div>
                    <h3 class="text-lg font-black uppercase tracking-wider text-black">
                        {{ Auth::user()->name ?? 'User Dossier' }}
                    </h3>
                    <p class="text-[9px] text-slate-500 font-extrabold uppercase tracking-widest mt-0.5">Verified Ledger
                        Identity</p>
                </div>
            </div>

            <!-- Profile Details Block -->
            <div class="mt-6 border-t-2 border-black pt-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white border border-black p-3.5 shadow-[2px_2px_0px_0px_#000]">
                        <span class="text-[7px] font-black uppercase tracking-widest text-slate-400 block mb-1">Access
                            Role</span>
                        <span
                            class="text-[10px] font-extrabold uppercase tracking-wider text-black bg-black/5 border border-black px-2 py-0.5 rounded-none inline-block">
                            {{ Auth::user()->role ?? 'Member' }}
                        </span>
                    </div>

                    <div class="bg-white border border-black p-3.5 shadow-[2px_2px_0px_0px_#000]">
                        <span
                            class="text-[7px] font-black uppercase tracking-widest text-slate-400 block mb-1">Workspace
                            Tenant</span>
                        <span
                            class="text-[10px] font-extrabold uppercase tracking-wider text-black truncate block max-w-full"
                            title="{{ Auth::user()->company_name ?? 'My Company' }}">
                            {{ Auth::user()->company_name ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <div class="bg-white border border-black p-4 shadow-[2px_2px_0px_0px_#000] space-y-3.5">
                    <div>
                        <span class="text-[7px] font-black uppercase tracking-widest text-slate-400 block mb-0.5">Email
                            Address</span>
                        <span
                            class="text-xs font-bold text-black font-mono break-all">{{ Auth::user()->email ?? 'N/A' }}</span>
                    </div>

                    <div class="h-px bg-black/10"></div>

                    <div>
                        <span
                            class="text-[7px] font-black uppercase tracking-widest text-slate-400 block mb-0.5">Database
                            Identifier</span>
                        <span
                            class="text-[10px] font-semibold text-slate-650 font-mono select-all">{{ Auth::user()->id ?? 'N/A' }}</span>
                    </div>

                    <div class="h-px bg-black/10"></div>

                    <div>
                        <span
                            class="text-[7px] font-black uppercase tracking-widest text-slate-400 block mb-0.5">Registration
                            Epoch</span>
                        <span
                            class="text-xs font-bold text-black">{{ Auth::user()->created_at ? Auth::user()->created_at->format('F d, Y @ H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="mt-6 text-center">
                <span
                    class="text-[8px] font-black uppercase tracking-widest text-slate-500 bg-white border border-black px-4 py-2 shadow-[2px_2px_0px_0px_#000]">
                    <i class="fa-solid fa-shield-halved text-emerald-500 mr-1 animate-pulse"></i> Session Securely
                    Encrypted
                </span>
            </div>

        </div>
    </div>

    <!-- 3. Dynamic Success/Error Toast Alert System -->
    @if(session('success'))
        <div id="toast-success"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 p-4 bg-white border-2 border-black text-black rounded-none shadow-[4px_4px_0px_0px_#000] transition-all duration-500 transform translate-y-0 opacity-100 max-w-sm">
            <div
                class="w-8 h-8 rounded-none bg-emerald-100 border border-black flex items-center justify-center text-black font-extrabold shadow-sm">
                <i class="fa-solid fa-circle-check text-base"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-bold text-black uppercase tracking-wider">Success</p>
                <p class="text-xs text-slate-650 font-semibold mt-0.5">{{ session('success') }}</p>
            </div>
            <button onclick="dismissToast('toast-success')"
                class="text-black hover:text-slate-600 transition-colors duration-150 p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="toast-error"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 p-4 bg-white border-2 border-black text-black rounded-none shadow-[4px_4px_0px_0px_#000] transition-all duration-500 transform translate-y-0 opacity-100 max-w-sm">
            <div
                class="w-8 h-8 rounded-none bg-rose-100 border border-black flex items-center justify-center text-black font-extrabold shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-bold text-black uppercase tracking-wider">Failed</p>
                <p class="text-xs text-slate-650 font-semibold mt-0.5">{{ session('error') }}</p>
            </div>
            <button onclick="dismissToast('toast-error')"
                class="text-black hover:text-slate-600 transition-colors duration-150 p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
    @endif

    <script>
        // Dismiss toasts
        function dismissToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }
        }

        // Manage Bell Icon Indicator Pings and Toast timers
        function initializeLayoutNotifications() {
            setTimeout(() => dismissToast('toast-success'), 5000);
            setTimeout(() => dismissToast('toast-error'), 5000);

            const company = "{{ auth()->user()->company_name ?? '' }}";
            const totalBroadcasts = {{ $totalCompanyBroadcasts ?? 0 }};
            if (!company) return;

            const storageKey = 'last_seen_broadcast_count_' + encodeURIComponent(company);
            const isChatPage = window.location.pathname.includes('/chat');
            if (isChatPage) {
                localStorage.setItem(storageKey, totalBroadcasts);
            }

            const lastSeenBroadcasts = parseInt(localStorage.getItem(storageKey) || '0');
            const broadcastUnread = Math.max(0, totalBroadcasts - lastSeenBroadcasts);
            const totalUnread = broadcastUnread;

            const ping = document.getElementById('bell-ping');
            const dot = document.getElementById('bell-dot');
            const dropdownBadge = document.getElementById('dropdown-badge');
            const notificationItems = document.querySelectorAll('.notification-item');
            const emptyState = document.getElementById('notifications-empty-state');

            if (notificationItems.length > 0) {
                if (totalUnread <= 0) {
                    notificationItems.forEach(item => item.classList.add('hidden'));
                    if (emptyState) emptyState.classList.remove('hidden');
                } else {
                    notificationItems.forEach((item, index) => {
                        if (index < totalUnread) {
                            item.classList.remove('hidden');
                        } else {
                            item.classList.add('hidden');
                        }
                    });
                    if (emptyState) emptyState.classList.add('hidden');
                }
            }

            if (totalUnread > 0) {
                if (ping) ping.classList.remove('hidden');
                if (dot) dot.classList.remove('hidden');
                if (dropdownBadge) {
                    dropdownBadge.textContent = totalUnread + " New";
                    dropdownBadge.classList.remove('hidden');
                }
            } else {
                if (ping) ping.classList.add('hidden');
                if (dot) dot.classList.add('hidden');
                if (dropdownBadge) dropdownBadge.classList.add('hidden');
            }
        }

        // Dynamic Profile Modal Controls
        function openProfileModal() {
            const modal = document.getElementById('profile-modal');
            const card = document.getElementById('profile-modal-card');
            if (modal && card) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    card.classList.remove('scale-95');
                    card.classList.add('scale-100');
                }, 10);
            }
        }

        function closeProfileModal() {
            const modal = document.getElementById('profile-modal');
            const card = document.getElementById('profile-modal-card');
            if (modal && card) {
                card.classList.remove('scale-100');
                card.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }, 150);
            }
        }

        // Dropdown Toggle and Close Controls
        function toggleNotificationsDropdown() {
            const dropdown = document.getElementById('notifications-dropdown');
            if (dropdown) {
                const isOpening = dropdown.classList.contains('hidden');
                dropdown.classList.toggle('hidden');

                if (isOpening) {
                    const company = "{{ auth()->user()->company_name ?? '' }}";
                    const totalBroadcasts = {{ $totalCompanyBroadcasts ?? 0 }};
                    const storageKey = 'last_seen_broadcast_count_' + encodeURIComponent(company);
                    localStorage.setItem(storageKey, totalBroadcasts);

                    const ping = document.getElementById('bell-ping');
                    const dot = document.getElementById('bell-dot');
                    const dropdownBadge = document.getElementById('dropdown-badge');

                    if (ping) ping.classList.add('hidden');
                    if (dot) dot.classList.add('hidden');
                    if (dropdownBadge) dropdownBadge.classList.add('hidden');

                    const notificationItems = document.querySelectorAll('.notification-item');
                    const emptyState = document.getElementById('notifications-empty-state');
                    if (notificationItems.length > 0) {
                        notificationItems.forEach(item => item.classList.add('hidden'));
                    }
                    if (emptyState) emptyState.classList.remove('hidden');
                }
            }
        }

        // Dynamic Dark Mode Controller
        function updateToggleIcon(theme, themeToggleIcon) {
            if (!themeToggleIcon) return;
            if (theme === 'dark') {
                themeToggleIcon.className = 'fa-solid fa-sun text-lg text-amber-400 animate-pulse';
            } else {
                themeToggleIcon.className = 'fa-solid fa-moon text-lg';
            }
        }

        // Initialize Bell, Search, Theme controls
        document.addEventListener('DOMContentLoaded', () => {
            initializeLayoutNotifications();

            // Initialize Lenis Kinetic Smooth Scrolling
            const scrollContainer = document.querySelector('main');
            const scrollContent = document.querySelector('.lenis-content');
            if (scrollContainer && scrollContent && typeof Lenis !== 'undefined') {
                const lenis = new Lenis({
                    wrapper: scrollContainer,
                    content: scrollContent,
                    duration: 1.2,
                    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                    orientation: 'vertical',
                    gestureOrientation: 'vertical',
                    smoothWheel: true,
                    wheelMultiplier: 1,
                    smoothTouch: false,
                    infinite: false,
                });

                function raf(time) {
                    lenis.raf(time);
                    requestAnimationFrame(raf);
                }
                requestAnimationFrame(raf);
            }

            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleIcon = document.getElementById('theme-toggle-icon');

            if (themeToggleBtn && themeToggleIcon) {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                updateToggleIcon(currentTheme, themeToggleIcon);

                themeToggleBtn.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-theme') || 'light';
                    const nextTheme = activeTheme === 'dark' ? 'light' : 'dark';

                    document.documentElement.setAttribute('data-theme', nextTheme);
                    localStorage.setItem('theme', nextTheme);
                    updateToggleIcon(nextTheme, themeToggleIcon);
                });
            }

            // Close notification dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const wrapper = document.getElementById('notifications-wrapper');
                const dropdown = document.getElementById('notifications-dropdown');
                if (wrapper && dropdown && !wrapper.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // ----------------------------------------------------
            // GLOBAL AUTOCOMPLETE SEARCH CONTROLLER
            // ----------------------------------------------------
            const searchInput = document.getElementById('global-search-input');
            const searchDropdown = document.getElementById('global-search-dropdown');
            const searchResults = document.getElementById('global-search-results');
            const searchSpinner = document.getElementById('global-search-spinner');
            let searchTimeout = null;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    clearTimeout(searchTimeout);

                    if (query.length < 2) {
                        searchDropdown.classList.add('hidden');
                        return;
                    }

                    if (searchSpinner) searchSpinner.classList.remove('hidden');

                    searchTimeout = setTimeout(() => {
                        fetch("/search/query?q=" + encodeURIComponent(query))
                            .then(response => response.json())
                            .then(data => {
                                if (searchSpinner) searchSpinner.classList.add('hidden');
                                if (!searchDropdown || !searchResults) return;

                                searchResults.innerHTML = '';
                                let html = '';
                                let hasResults = false;

                                if (data.employees && data.employees.length > 0) {
                                    hasResults = true;
                                    html += `<div class="p-2 bg-[#F4ECE6] border-b border-black text-[9px] font-black uppercase tracking-wider text-black flex items-center gap-1.5"><i class="fa-solid fa-users text-amber-600"></i> Employees</div>`;
                                    data.employees.forEach(emp => {
                                        const clickHandler = emp.url !== '#' ? `href="${emp.url}"` : `onclick="alert('Employee Dossier:\\nName: ${emp.name}\\nEmail: ${emp.email}\\nDept: ${emp.department}\\nRole: ${emp.designation}')" style="cursor: pointer;"`;
                                        html += `
                                            <a ${clickHandler} class="block p-3 hover:bg-neutral-50 transition-colors">
                                                <p class="text-xs font-black text-black leading-tight">${emp.name}</p>
                                                <p class="text-[9px] font-semibold text-slate-550 mt-0.5">${emp.designation} • ${emp.department}</p>
                                            </a>
                                        `;
                                    });
                                }

                                if (data.announcements && data.announcements.length > 0) {
                                    hasResults = true;
                                    html += `<div class="p-2 bg-[#F4ECE6] border-b border-black border-t border-black/10 text-[9px] font-black uppercase tracking-wider text-black flex items-center gap-1.5"><i class="fa-solid fa-bullhorn text-amber-500"></i> Announcements</div>`;
                                    data.announcements.forEach(ann => {
                                        html += `
                                            <a href="${ann.url}" class="block p-3 hover:bg-neutral-50 transition-colors">
                                                <div class="flex items-center justify-between gap-2">
                                                    <p class="text-[10px] font-black text-black">${ann.sender}</p>
                                                    <span class="text-[8px] font-bold text-slate-455 uppercase tracking-wider">${ann.created_at}</span>
                                                </div>
                                                <p class="text-[9px] font-semibold text-slate-700 mt-1 italic">"${ann.snippet}"</p>
                                            </a>
                                        `;
                                    });
                                }

                                if (data.payrolls && data.payrolls.length > 0) {
                                    hasResults = true;
                                    html += `<div class="p-2 bg-[#F4ECE6] border-b border-black border-t border-black/10 text-[9px] font-black uppercase tracking-wider text-black flex items-center gap-1.5"><i class="fa-solid fa-wallet text-amber-600"></i> Payouts</div>`;
                                    data.payrolls.forEach(pay => {
                                        html += `
                                            <a href="${pay.url}" target="_blank" class="block p-3 hover:bg-neutral-50 transition-colors">
                                                <p class="text-xs font-black text-black leading-tight">${pay.month} Slip</p>
                                            </a>
                                        `;
                                    });
                                }

                                if (!hasResults) {
                                    html = `<div class="p-6 text-center text-xs text-slate-500 font-bold uppercase bg-white">No matches found</div>`;
                                }

                                searchResults.innerHTML = html;
                                searchDropdown.classList.remove('hidden');
                            });
                    }, 250);
                });

                // Close search dropdown on click outside
                document.addEventListener('click', function(e) {
                    const container = document.getElementById('global-search-container');
                    if (container && !container.contains(e.target)) {
                        searchDropdown.classList.add('hidden');
                    }
                });
            }

            // Close dropdowns on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const searchDropdown = document.getElementById('global-search-dropdown');
                    if (searchDropdown) searchDropdown.classList.add('hidden');

                    const notifDropdown = document.getElementById('notifications-dropdown');
                    if (notifDropdown) notifDropdown.classList.add('hidden');
                }
            });
        });
    </script>
    @yield('scripts')
</body>

</html>