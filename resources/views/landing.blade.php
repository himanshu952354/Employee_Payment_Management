<!DOCTYPE html>
<html lang="en" class="bg-[#F4ECE6]">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayFlow - Enterprise Payment & Payouts Engine</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lenis Smooth Scroll Engine -->
    <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>

    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html.lenis, html.lenis body {
            height: auto;
        }

        .lenis.lenis-smooth {
            scroll-behavior: auto !important;
        }

        .lenis.lenis-smooth [data-lenis-prevent] {
            overscroll-behavior: contain;
        }

        .lenis.lenis-stopped {
            overflow: hidden;
        }

        .lenis.lenis-scrolling iframe {
            pointer-events: none;
        }

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

        /* Brutalist Theme Overrides for Clerk Components */
        .cl-footer,
        .cl-internal-b3mqbw,
        .cl-internal-107sb90,
        .cl-internal-172zthp,
        .cl-internal-180g7wz,
        [class*="cl-badge"],
        [class*="cl-footer"],
        [class*="cl-internal-"] {
            display: none !important;
        }

        .cl-formButtonPrimary {
            background-color: black !important;
            border-radius: 0px !important;
            font-family: 'Outfit', sans-serif !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            border: 2px solid black !important;
            box-shadow: 3px 3px 0px 0px rgba(0, 0, 0, 1) !important;
            transition: all 0.15s ease !important;
        }

        .cl-formButtonPrimary:hover {
            background-color: #222 !important;
        }

        .cl-formButtonPrimary:active {
            transform: translate(2px, 2px) !important;
            box-shadow: 0px 0px 0px 0px rgba(0, 0, 0, 1) !important;
        }

        .cl-formFieldInput {
            border-radius: 0px !important;
            border: 2px solid black !important;
            background-color: #F4ECE6 !important;
            color: black !important;
            font-family: 'Outfit', sans-serif !important;
            font-weight: 700 !important;
        }

        .cl-formFieldInput:focus {
            border-color: black !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .cl-card {
            box-shadow: none !important;
            border: none !important;
            background-color: transparent !important;
        }

        .cl-headerTitle {
            font-family: 'Outfit', sans-serif !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        .cl-headerSubtitle {
            font-family: 'Outfit', sans-serif !important;
            font-weight: 700 !important;
        }

        .cl-dividerText {
            font-family: 'Outfit', sans-serif !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
        }

        .cl-socialButtonsIconButton {
            border-radius: 0px !important;
            border: 2px solid black !important;
            box-shadow: 2px 2px 0px 0px rgba(0, 0, 0, 1) !important;
            background-color: white !important;
        }

        .cl-socialButtonsIconButton:active {
            transform: translate(1px, 1px) !important;
            box-shadow: 0px 0px 0px 0px rgba(0, 0, 0, 1) !important;
        }
    </style>
</head>

<body class="min-h-screen text-black bg-[#F4ECE6] flex flex-col justify-between overflow-x-hidden relative antialiased">

    <!-- Ambient retro graphic lines -->
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none z-0"
        style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;"></div>

    <!-- 1. Header / Navigation Bar -->
    <header class="w-full py-5 px-6 md:px-12 bg-[#F4ECE6] border-b border-black sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-xl font-black uppercase tracking-tight text-black select-none">PayFlow</span>
            </div>

            <div class="flex items-center gap-6 sm:gap-8">
                <!-- Navigation Links -->
                <nav
                    class="hidden lg:flex items-center gap-5 xl:gap-8 text-[10px] font-black uppercase tracking-wider text-black mr-2">
                    <a href="#features" class="hover:underline underline-offset-4 decoration-2">Features</a>
                    <a href="#demo" class="hover:underline underline-offset-4 decoration-2">Dashboard Demo</a>
                    <a href="#pricing" class="hover:underline underline-offset-4 decoration-2">Pricing</a>
                    <a href="#faq" class="hover:underline underline-offset-4 decoration-2">FAQ</a>
                    <a href="#contact" class="hover:underline underline-offset-4 decoration-2">Contact Us</a>
                </nav>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <button onclick="openAuthModal('login')"
                        class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-black hover:bg-black/5 py-2.5 px-3 sm:px-4 border border-transparent transition-all">
                        Log In
                    </button>
                    <button onclick="openAuthModal('signup')"
                        class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider bg-black hover:bg-neutral-800 text-white py-2.5 px-4 sm:px-5 rounded-none border border-black transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                        Start Free
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. Hero Presentation Section -->
    <main
        class="flex-1 flex flex-col justify-center items-center px-6 py-16 md:py-24 max-w-7xl mx-auto text-center z-10 relative w-full">
        <span
            class="text-[9px] px-3.5 py-1.5 rounded-none font-bold bg-white text-black border border-black uppercase tracking-widest mb-6 shadow-[2px_2px_0px_0px_#000]">
            Introducing PayFlow SaaS 2.0
        </span>
        <h1 class="text-4xl md:text-6xl font-black tracking-tight text-black leading-none uppercase">
            Streamline Employee Payments & <br>
            <span class="underline decoration-wavy decoration-black underline-offset-8">Transaction Ledgers</span>
        </h1>
        <p class="text-slate-700 text-xs md:text-sm max-w-2xl mt-8 leading-relaxed font-bold">
            An enterprise-grade, high-fidelity payroll processing workspace designed to connect HR command desks with
            secure employee self-service portals. Built with bank-grade ledger protocols.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center gap-4 mt-12 w-full sm:w-auto">
            <button onclick="openAuthModal('signup')"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-4 rounded-none bg-black hover:bg-neutral-800 font-extrabold text-xs uppercase tracking-wider text-white border border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-x-[3px] active:translate-y-[3px] active:shadow-none">
                <i class="fa-solid fa-rocket"></i> Start Free Company Trial
            </button>
            <a href="#demo"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-4 rounded-none bg-white border border-black hover:bg-slate-50 font-extrabold text-xs uppercase tracking-wider text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-x-[3px] active:translate-y-[3px] active:shadow-none">
                <i class="fa-solid fa-play-circle text-base"></i> Launch Workspace Demo
            </a>
        </div>

        <!-- 3. Dynamic Feature Showcase Cards Grid -->
        <section id="features" class="w-full mt-32 text-left scroll-mt-24">
            <div class="border-b border-black pb-4 mb-12 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-550">Engine
                        Capabilities</span>
                    <h2 class="text-3xl font-black uppercase tracking-tight text-black mt-1">Core Architecture Modules
                    </h2>
                </div>
                <p class="text-xs text-slate-600 font-bold max-w-md">Every workflow you need to register rosters, manage
                    attendances, adjust balances, and disburse corporate payroll packages.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div
                    class="bg-white border-2 border-black p-6 rounded-none space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div
                        class="w-10 h-10 rounded-none bg-black text-white flex items-center justify-center border border-black">
                        <i class="fa-solid fa-chart-pie text-base"></i>
                    </div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-black">Executive Dashboards</h3>
                    <p class="text-xs text-slate-650 font-semibold leading-relaxed">
                        Aggregate total hire registers, monthly outflows, roster attendances, and slice headcount via
                        interactive Chart.js graphs.
                    </p>
                </div>

                <!-- Card 2 -->
                <div
                    class="bg-white border-2 border-black p-6 rounded-none space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div
                        class="w-10 h-10 rounded-none bg-black text-white flex items-center justify-center border border-black">
                        <i class="fa-solid fa-cash-register text-base"></i>
                    </div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-black">Disbursing Ledger</h3>
                    <p class="text-xs text-slate-650 font-semibold leading-relaxed">
                        Generate payroll billing periods, apply custom bonuses or taxes, and clear payments with
                        simulated wire APIs in one click.
                    </p>
                </div>

                <!-- Card 3 -->
                <div
                    class="bg-white border-2 border-black p-6 rounded-none space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div
                        class="w-10 h-10 rounded-none bg-black text-white flex items-center justify-center border border-black">
                        <i class="fa-solid fa-users-viewfinder text-base"></i>
                    </div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-black">Self-Service Dossier</h3>
                    <p class="text-xs text-slate-650 font-semibold leading-relaxed">
                        Allow staff members to log in, review their personal attendance scores, audit banking
                        coordinates, and print salary slips.
                    </p>
                </div>

                <!-- Card 4 -->
                <div
                    class="bg-white border-2 border-black p-6 rounded-none space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div
                        class="w-10 h-10 rounded-none bg-black text-white flex items-center justify-center border border-black">
                        <i class="fa-solid fa-calendar-days text-base"></i>
                    </div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-black"> Roster Attendance</h3>
                    <p class="text-xs text-slate-650 font-semibold leading-relaxed">
                        Process check-ins using segmented roster switches (Present, Leave, Absent) and track daily
                        attendance rates instantly.
                    </p>
                </div>

                <!-- Card 5 -->
                <div
                    class="bg-white border-2 border-black p-6 rounded-none space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div
                        class="w-10 h-10 rounded-none bg-black text-white flex items-center justify-center border border-black">
                        <i class="fa-solid fa-file-pdf text-base"></i>
                    </div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-black">PDF Invoicing Slips</h3>
                    <p class="text-xs text-slate-650 font-semibold leading-relaxed">
                        Automatically output highly optimized printable invoice stubs featuring unique,
                        cryptographically secure hash coordinates.
                    </p>
                </div>

                <!-- Card 6 -->
                <div
                    class="bg-white border-2 border-black p-6 rounded-none space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div
                        class="w-10 h-10 rounded-none bg-black text-white flex items-center justify-center border border-black">
                        <i class="fa-solid fa-shield-halved text-base"></i>
                    </div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-black">Bank-Grade Gateway</h3>
                    <p class="text-xs text-slate-650 font-semibold leading-relaxed">
                        Direct support for mock bank wires, PayPal APIs, Stripe billing mechanisms, and cash log
                        clearings.
                    </p>
                </div>
            </div>
        </section>

        <!-- 4. Interactive High-Fidelity Dashboard Demo -->
        <section id="demo" class="w-full mt-32 text-left scroll-mt-24">
            <div class="border-b border-black pb-4 mb-12">
                <span class="text-[9px] font-black uppercase tracking-wider text-slate-550">Interactive Workspace
                    Preview</span>
                <h2 class="text-3xl font-black uppercase tracking-tight text-black mt-1">Live Dashboard Interface</h2>
            </div>

            <!-- Demo Container Box (Replicating real portal dashboard styling!) -->
            <div
                class="bg-white border-2 border-black rounded-none shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
                <!-- Mock Top Title Bar -->
                <div class="bg-[#F4ECE6] border-b-2 border-black px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 bg-rose-500 border border-black inline-block"></span>
                        <span class="w-3.5 h-3.5 bg-amber-400 border border-black inline-block"></span>
                        <span class="w-3.5 h-3.5 bg-emerald-500 border border-black inline-block"></span>
                        <span class="text-xs font-black uppercase tracking-wider text-black ml-3">PayFlow OS Terminal
                            v2.0 - Active Node</span>
                    </div>
                    <span
                        class="text-[9px] font-black bg-black text-white px-2 py-0.5 uppercase tracking-widest">Administrator</span>
                </div>

                <!-- Mock Dashboard Body Workspace -->
                <div class="bg-[#F4ECE6]/50 p-6 space-y-6">
                    <!-- Metric Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white border border-black p-4 shadow-[3px_3px_0px_0px_#000]">
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Total Active
                                Roster</span>
                            <div class="flex justify-between items-end mt-2">
                                <span class="text-2xl font-black text-black">24</span>
                                <span class="text-[9px] font-bold text-slate-600 uppercase">Employees Joined</span>
                            </div>
                        </div>
                        <div class="bg-white border border-black p-4 shadow-[3px_3px_0px_0px_#000]">
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Monthly
                                Clearances</span>
                            <div class="flex justify-between items-end mt-2">
                                <span class="text-2xl font-black text-black">$128,450.00</span>
                                <span class="text-[9px] font-bold text-emerald-700 uppercase">Paid Out</span>
                            </div>
                        </div>
                        <div class="bg-white border border-black p-4 shadow-[3px_3px_0px_0px_#000]">
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Roster
                                Attendance Rate</span>
                            <div class="flex justify-between items-end mt-2">
                                <span class="text-2xl font-black text-black">98.4%</span>
                                <span class="text-[9px] font-bold text-indigo-700 uppercase">Operational</span>
                            </div>
                        </div>
                    </div>

                    <!-- Split Panels (Chart & Transactions) -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Chart Mock (SVG drawing of line graph!) -->
                        <div
                            class="lg:col-span-2 bg-white border border-black p-6 shadow-[3px_3px_0px_0px_#000] flex flex-col justify-between">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-wider text-black">Expenditure
                                    Trend Graph</span>
                                <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">Clearing trends over
                                    last six billing cycles</p>
                            </div>
                            <div class="mt-6 h-48 w-full flex items-end">
                                <!-- Simulated SVG Brutalist Line Chart -->
                                <svg class="w-full h-full" viewBox="0 0 500 150" preserveAspectRatio="none">
                                    <!-- Grid lines -->
                                    <line x1="0" y1="30" x2="500" y2="30" stroke="#E2E8F0" stroke-width="1" />
                                    <line x1="0" y1="70" x2="500" y2="70" stroke="#E2E8F0" stroke-width="1" />
                                    <line x1="0" y1="110" x2="500" y2="110" stroke="#E2E8F0" stroke-width="1" />
                                    <!-- Trend lines -->
                                    <polyline fill="rgba(0,0,0,0.05)" stroke="#000000" stroke-width="3" points="
                                        0,120
                                        100,105
                                        200,75
                                        300,85
                                        400,45
                                        500,20
                                    " />
                                    <!-- Points -->
                                    <circle cx="100" cy="105" r="5" fill="#000" stroke="#fff" stroke-width="2" />
                                    <circle cx="200" cy="75" r="5" fill="#000" stroke="#fff" stroke-width="2" />
                                    <circle cx="300" cy="85" r="5" fill="#000" stroke="#fff" stroke-width="2" />
                                    <circle cx="400" cy="45" r="5" fill="#000" stroke="#fff" stroke-width="2" />
                                    <circle cx="500" cy="20" r="5" fill="#000" stroke="#fff" stroke-width="2" />
                                </svg>
                            </div>
                            <div
                                class="flex justify-between items-center text-[9px] font-black uppercase text-black mt-4 pt-2 border-t border-black/10">
                                <span>Nov</span>
                                <span>Dec</span>
                                <span>Jan</span>
                                <span>Feb</span>
                                <span>Mar</span>
                                <span>Apr</span>
                            </div>
                        </div>

                        <!-- Mini list -->
                        <div
                            class="bg-white border border-black p-6 shadow-[3px_3px_0px_0px_#000] flex flex-col justify-between">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-wider text-black">Active Team
                                    Roster</span>
                                <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">Top-earning team leads
                                </p>
                            </div>
                            <div class="mt-4 space-y-3">
                                <div class="flex items-center justify-between text-xs border-b border-black/10 pb-2">
                                    <span class="font-extrabold text-black">Himanshu Shekhar</span>
                                    <span class="font-mono text-slate-500">$9,500/mo</span>
                                </div>
                                <div class="flex items-center justify-between text-xs border-b border-black/10 pb-2">
                                    <span class="font-extrabold text-black">Aarav Sharma</span>
                                    <span class="font-mono text-slate-500">$8,200/mo</span>
                                </div>
                                <div class="flex items-center justify-between text-xs border-b border-black/10 pb-2">
                                    <span class="font-extrabold text-black">Nisha Patel</span>
                                    <span class="font-mono text-slate-500">$7,800/mo</span>
                                </div>
                                <div class="flex items-center justify-between text-xs pb-1">
                                    <span class="font-extrabold text-black">Rohan Gupta</span>
                                    <span class="font-mono text-slate-500">$6,400/mo</span>
                                </div>
                            </div>
                            <div class="bg-[#F4ECE6] border border-black p-3 text-[9px] text-black font-semibold mt-4">
                                Demo database represents real transaction ledgers.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4.5 SaaS Pricing Grid -->
        <section id="pricing" class="w-full mt-32 text-left scroll-mt-24">
            <div class="border-b border-black pb-4 mb-12 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-550">Transparent
                        Pricing</span>
                    <h2 class="text-3xl font-black uppercase tracking-tight text-black mt-1">Select Corporate Plan</h2>
                </div>
                <p class="text-xs text-slate-655 font-bold max-w-sm">Honest pricing without hidden transaction fees.
                    Clear billing sheets cleared at fixed monthly intervals.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Tier 1: Starter -->
                <div
                    class="bg-white border-2 border-black p-6 rounded-none flex flex-col justify-between shadow-[4px_4px_0px_0px_#000] relative">
                    <div>
                        <span
                            class="text-[9px] px-2 py-0.5 font-bold bg-[#F4ECE6] text-black border border-black uppercase tracking-widest inline-block mb-4">Starter</span>
                        <h3 class="text-3xl font-black text-black">$19<span
                                class="text-xs font-bold text-slate-500">/month</span></h3>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">For growing teams</p>

                        <ul class="mt-6 space-y-3.5 text-xs font-bold text-slate-700">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i> Up
                                to 10 Employees</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Manual Daily Attendance</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Basic Payroll Calculations</li>
                            <li class="flex items-center gap-2 text-slate-400"><i class="fa-solid fa-circle-xmark"></i>
                                Advanced Analytics</li>
                            <li class="flex items-center gap-2 text-slate-400"><i class="fa-solid fa-circle-xmark"></i>
                                Custom Wire Gateways</li>
                        </ul>
                    </div>
                    <button onclick="openAuthModal('signup')"
                        class="w-full mt-8 py-3 bg-[#F4ECE6] border border-black text-black font-extrabold text-[10px] uppercase tracking-wider hover:bg-black hover:text-white transition-all shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none">
                        Choose Starter
                    </button>
                </div>

                <!-- Tier 2: Pro Command (BEST VALUE) -->
                <div
                    class="bg-[#F4ECE6] border-2 border-black p-6 rounded-none flex flex-col justify-between shadow-[6px_6px_0px_0px_#000] relative transform md:-translate-y-2">
                    <span
                        class="absolute top-0 right-6 -translate-y-1/2 text-[9px] px-3.5 py-1.5 font-black bg-black text-white border border-black uppercase tracking-widest shadow-[2px_2px_0px_0px_#000]">
                        Best Value
                    </span>
                    <div>
                        <span
                            class="text-[9px] px-2 py-0.5 font-bold bg-black text-white border border-black uppercase tracking-widest inline-block mb-4">Pro
                            Command</span>
                        <h3 class="text-3xl font-black text-black">$49<span
                                class="text-xs font-bold text-slate-650">/month</span></h3>
                        <p class="text-xs text-slate-655 font-bold uppercase tracking-wider mt-1">For standard companies
                        </p>

                        <ul class="mt-6 space-y-3.5 text-xs font-bold text-black">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i> Up
                                to 50 Employees</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Segmented Attendance Roster</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Financial Adjust Allowances</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Dynamic Chart.js Analytics</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Printable Salary Slips</li>
                        </ul>
                    </div>
                    <button onclick="openAuthModal('signup')"
                        class="w-full mt-8 py-3.5 bg-black text-white font-extrabold text-[10px] uppercase tracking-wider hover:bg-neutral-800 transition-all shadow-[3px_3px_0px_0px_#000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                        Deploy Pro Workspace
                    </button>
                </div>

                <!-- Tier 3: Enterprise Core -->
                <div
                    class="bg-white border-2 border-black p-6 rounded-none flex flex-col justify-between shadow-[4px_4px_0px_0px_#000] relative">
                    <div>
                        <span
                            class="text-[9px] px-2 py-0.5 font-bold bg-[#F4ECE6] text-black border border-black uppercase tracking-widest inline-block mb-4">Enterprise</span>
                        <h3 class="text-3xl font-black text-black">$129<span
                                class="text-xs font-bold text-slate-500">/month</span></h3>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">For large
                            conglomerates</p>

                        <ul class="mt-6 space-y-3.5 text-xs font-bold text-slate-700">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Unlimited Employees</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Custom Wire Gateways</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Cryptographic Slip Signings</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Dedicated 24/7 Ops Support</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-black"></i>
                                Automated CSV Exports</li>
                        </ul>
                    </div>
                    <button onclick="openAuthModal('signup')"
                        class="w-full mt-8 py-3 bg-[#F4ECE6] border border-black text-black font-extrabold text-[10px] uppercase tracking-wider hover:bg-black hover:text-white transition-all shadow-[2px_2px_0px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none">
                        Deploy Enterprise
                    </button>
                </div>
            </div>
        </section>

        <!-- 4.8 FAQ Accordion Section -->
        <section id="faq" class="w-full mt-32 text-left scroll-mt-24">
            <div class="border-b border-black pb-4 mb-12">
                <span class="text-[9px] font-black uppercase tracking-wider text-slate-550">Help & Knowledge
                    Center</span>
                <h2 class="text-3xl font-black uppercase tracking-tight text-black mt-1">Frequently Asked Questions</h2>
            </div>

            <div class="space-y-6">
                <!-- FAQ Item 1 -->
                <div class="bg-white border-2 border-black p-6 rounded-none shadow-[3px_3px_0px_0px_#000]">
                    <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-black"></i>
                        How secure are PayFlow's simulated clearance logs?
                    </h3>
                    <p
                        class="text-xs text-slate-650 font-bold uppercase tracking-wider leading-relaxed mt-3 pl-6 border-l border-black/25">
                        PayFlow is designed with modern enterprise paradigms. All clearings are fully verified, logged
                        locally with reference comments, and recorded inside high-fidelity database structures,
                        completely matching standard cryptographic banking wire schemas.
                    </p>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white border-2 border-black p-6 rounded-none shadow-[3px_3px_0px_0px_#000]">
                    <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                        <i class="fa-solid fa-users text-black"></i>
                        Can staff members print their own salary slip sheets?
                    </h3>
                    <p
                        class="text-xs text-slate-655 font-bold uppercase tracking-wider leading-relaxed mt-3 pl-6 border-l border-black/25">
                        Yes! Upon corporate signup, an admin has a directory to set up active employees. Each employee
                        receives automated, secure self-service credentials to check their personal logs, audit payment
                        channels, and print salary stubs.
                    </p>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white border-2 border-black p-6 rounded-none shadow-[3px_3px_0px_0px_#000]">
                    <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-black"></i>
                        Can I apply custom monthly bonuses and tax offsets?
                    </h3>
                    <p
                        class="text-xs text-slate-650 font-bold uppercase tracking-wider leading-relaxed mt-3 pl-6 border-l border-black/25">
                        Absolutely. Inside the payroll operations desk, you can click "Adjust Financials" on any
                        unprocessed or pending billing cycle to add specific bonuses or deduct tax volumes in real time
                        before clearing the payout.
                    </p>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-white border-2 border-black p-6 rounded-none shadow-[3px_3px_0px_0px_#000]">
                    <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                        <i class="fa-solid fa-circle-play text-black"></i>
                        Is there a fully featured trial mode available?
                    </h3>
                    <p
                        class="text-xs text-slate-650 font-bold uppercase tracking-wider leading-relaxed mt-3 pl-6 border-l border-black/25">
                        Yes! We have pre-provisioned a live workspace demo. Just click the "Launch Workspace Demo" CTA
                        or trigger the log in modal and select "Admin" or "Employee" to load ready-to-test mock
                        credentials.
                    </p>
                </div>
            </div>
        </section>

        <!-- 5. Brutalist Corporate Contact Us Form -->
        <section id="contact" class="w-full mt-32 text-left scroll-mt-24">
            <div class="border-b border-black pb-4 mb-12">
                <span class="text-[9px] font-black uppercase tracking-wider text-slate-550">Direct Communication
                    Hub</span>
                <h2 class="text-3xl font-black uppercase tracking-tight text-black mt-1">Get In Touch</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-start">
                <!-- Left 3 Columns: Form -->
                <div
                    class="lg:col-span-3 bg-white border-2 border-black rounded-none p-8 shadow-[8px_8px_0px_0px_#000]">
                    <form
                        onsubmit="event.preventDefault(); alert('Demo Message Sent! In a production system, this registers a customer ticket.');"
                        class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Full
                                    Name</label>
                                <input type="text" required
                                    class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all"
                                    placeholder="e.g. Himanshu Shekhar">
                            </div>
                            <div>
                                <label
                                    class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Corporate
                                    Email</label>
                                <input type="email" required
                                    class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all"
                                    placeholder="e.g. contact@domain.com">
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-[9px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Your
                                Message Inquiry</label>
                            <textarea required rows="4"
                                class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all resize-none"
                                placeholder="How can PayFlow streamline your organizational cash clearance flows?"></textarea>
                        </div>

                        <button type="submit"
                            class="flex items-center justify-center gap-2 px-8 py-4 rounded-none bg-black hover:bg-neutral-800 text-white font-extrabold text-xs uppercase tracking-wider border border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-x-[3px] active:translate-y-[3px] active:shadow-none">
                            <i class="fa-solid fa-paper-plane"></i> Dispatch Inquiry Ticket
                        </button>
                    </form>
                </div>

                <!-- Right 2 Columns: Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-3">
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Headquarters
                            Address</span>
                        <p class="font-extrabold text-sm text-black">PayFlow Technologies Inc.</p>
                        <p class="text-xs text-slate-650 font-bold leading-relaxed">
                            722 Brutalist Avenue, Suite 100<br>
                            SaaS District, Tech Valley CA 94025
                        </p>
                    </div>

                    <div
                        class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-3">
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Support
                            Coordinates</span>
                        <div class="space-y-1.5 text-xs font-bold">
                            <div class="flex justify-between">
                                <span class="text-slate-500 uppercase tracking-wider text-[9px]">General:</span>
                                <span class="text-black">general@payflow.com</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 uppercase tracking-wider text-[9px]">Support Desk:</span>
                                <span class="text-black">ops@payflow.com</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 uppercase tracking-wider text-[9px]">Hotline:</span>
                                <span class="text-black">+1 (800) 555-FLOW</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer
        class="w-full py-6 px-6 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500 border-t border-black z-10 relative">
        <p>&copy; 2026 PayFlow Inc. All rights reserved. Secured with cryptographic bank-level ledger APIs.</p>
    </footer>

    <div id="auth-modal" onclick="if(event.target === this) closeAuthModal()"
        class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
        <div class="bg-white border-2 border-black rounded-none shadow-[8px_8px_0px_0px_#000] max-w-md w-full relative z-10 overflow-hidden transform scale-95 transition-transform duration-200"
            id="auth-modal-card">

            <!-- Modal Title/Header Bar -->
            <div class="bg-[#F4ECE6] border-b border-black px-4 py-3 flex items-center justify-between select-none">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 bg-rose-500 border border-black inline-block rounded-full"></span>
                    <span class="w-2.5 h-2.5 bg-amber-400 border border-black inline-block rounded-full"></span>
                    <span class="w-2.5 h-2.5 bg-emerald-500 border border-black inline-block rounded-full"></span>
                    <span class="text-[10px] font-black uppercase tracking-wider text-black ml-2 select-none">Auth
                        Terminal</span>
                </div>
                <!-- Active close button inside the header bar! -->
                <button onclick="closeAuthModal()"
                    class="text-black hover:bg-black hover:text-white border border-black p-1.5 transition-all bg-white flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none">
                    <i class="fa-solid fa-xmark text-xs font-black"></i>
                </button>
            </div>

            <!-- 1. Tabs Header (Log In vs Sign Up) -->
            <div
                class="grid grid-cols-2 bg-[#F4ECE6] border-b border-black font-extrabold text-xs uppercase tracking-wider select-none">
                <button onclick="switchTab('login')" id="tab-login-btn"
                    class="py-4 border-b-2 border-black text-black transition-all text-center">
                    Log In Workspace
                </button>
                <button onclick="switchTab('signup')" id="tab-signup-btn"
                    class="py-4 border-b-2 border-transparent text-slate-500 hover:text-black transition-all text-center">
                    Create New Account
                </button>
            </div>

            <div class="p-6">
                <!-- 2. Dual-Role Selector Switches -->
                <div class="mb-6">
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-2">Access
                        Authority Role</label>
                    <div class="grid grid-cols-2 bg-[#F4ECE6] p-1 rounded-none border border-black shadow-sm">
                        <button onclick="switchRole('admin')" id="role-admin-btn" type="button"
                            class="py-2.5 rounded-none text-[10px] font-bold uppercase tracking-wider text-white bg-black border border-black transition-all text-center shadow-sm">
                            <i class="fa-solid fa-user-tie mr-1 text-[10px]"></i> Admin (Boss)
                        </button>
                        <button onclick="switchRole('employee')" id="role-employee-btn" type="button"
                            class="py-2.5 rounded-none text-[10px] font-bold uppercase tracking-wider text-slate-650 hover:text-black transition-all text-center">
                            <i class="fa-solid fa-user-tag mr-1 text-[10px]"></i> Employee
                        </button>
                    </div>
                </div>

                <!-- 3. Form 1: LOG IN -->
                <form id="login-form" action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="role" id="login-role-input" value="admin">

                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5">Email
                            Address</label>
                        <input type="email" name="email" required
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all placeholder-slate-400"
                            placeholder="e.g. boss@company.com">
                    </div>

                    <div>
                        <label
                            class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5">Password</label>
                        <input type="password" name="password" required
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all placeholder-slate-400"
                            placeholder="••••••••">
                    </div>

                    <button type="submit"
                        class="w-full py-4 rounded-none bg-black hover:bg-neutral-800 text-white font-extrabold text-xs uppercase tracking-wider border border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none mt-4 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-shield-halved"></i> Login
                    </button>
                </form>

                <!-- 4. Form 2: SIGN UP (Initially Hidden) -->
                <form id="signup-form" action="{{ route('signup') }}" method="POST" class="space-y-4 hidden">
                    @csrf
                    <input type="hidden" name="role" id="signup-role-input" value="admin">

                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5">Full
                            Name</label>
                        <input type="text" name="name" required
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all placeholder-slate-400"
                            placeholder="e.g. John Doe">
                    </div>

                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5">Email
                            Address</label>
                        <input type="email" name="email" required
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all placeholder-slate-400"
                            placeholder="e.g. boss@company.com">
                    </div>

                    <!-- Company Name (Show/Hide dynamically!) -->
                    <div id="company-name-field" class="mb-4">
                        <label
                            class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5">Company
                            Name</label>
                        <input type="text" name="company_name" id="company_name_input" required
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all placeholder-slate-400"
                            placeholder="e.g. Acme Corp">
                    </div>

                    <!-- Company Departments (Show/Hide dynamically!) -->
                    <div id="company-departments-field" class="mb-4">
                        <label
                            class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5">Company
                            Departments (Comma-separated)</label>
                        <input type="text" name="departments" id="company_departments_input" required
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all placeholder-slate-400"
                            placeholder="e.g. Engineering, Marketing, HR, Finance, Sales"
                            value="Engineering, Marketing, HR, Finance, Sales">
                    </div>

                    <!-- Company Currency (Show/Hide dynamically!) -->
                    <div id="company-currency-field" class="mb-4">
                        <label
                            class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5">Preferred
                            Currency Symbol</label>
                        <select name="currency" id="company_currency_input" required
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold uppercase tracking-wider transition-all">
                            <option value="$">USD ($) - Dollar</option>
                            <option value="₹">INR (₹) - Rupee</option>
                            <option value="€">EUR (€) - Euro</option>
                            <option value="£">GBP (£) - Pound</option>
                            <option value="¥">JPY (¥) - Yen</option>
                            <option value="A$">AUD (A$) - Aus Dollar</option>
                            <option value="C$">CAD (C$) - Can Dollar</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-[9px] font-black uppercase tracking-wider text-slate-550 mb-1.5">Password</label>
                        <input type="password" name="password" required
                            class="block w-full rounded-none border border-black bg-[#F4ECE6] py-3 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all placeholder-slate-400"
                            placeholder="Minimum 6 characters">
                    </div>

                    <!-- Company signup tip -->
                    <div id="signup-tip"
                        class="p-3 bg-[#F4ECE6] border border-black rounded-none text-[10px] text-black font-semibold flex items-start gap-2 mb-4">
                        <i class="fa-solid fa-shield-halved mt-0.5 text-black font-bold"></i>
                        <span>Registering as Admin (Boss) automatically provisions a brand new company payment workspace
                            instantly.</span>
                    </div>

                    <button type="submit"
                        class="w-full py-4 rounded-none bg-black hover:bg-neutral-800 text-white font-extrabold text-xs uppercase tracking-wider border border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none mt-6 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rocket"></i> Signup
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= GLOBAL TOAST ALERTS ================= -->
    @if(session('success'))
        <div id="toast-success"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 p-4 bg-white border-2 border-black text-black rounded-none shadow-[4px_4px_0px_0px_#000] max-w-sm">
            <div
                class="w-8 h-8 rounded-none bg-emerald-100 border border-black flex items-center justify-center text-black font-bold shadow-sm">
                <i class="fa-solid fa-circle-check text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[9px] font-black uppercase tracking-wider text-black">Success</p>
                <p class="text-xs text-slate-650 font-semibold mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div id="toast-error"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 p-4 bg-white border-2 border-black text-black rounded-none shadow-[4px_4px_0px_0px_#000] max-w-sm">
            <div
                class="w-8 h-8 rounded-none bg-rose-100 border border-black flex items-center justify-center text-black font-bold shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[9px] font-black uppercase tracking-wider text-black">Authentication Error</p>
                <p class="text-xs text-slate-650 font-semibold mt-0.5">
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        {{ $errors->first() }}
                    @endif
                </p>
            </div>
        </div>
    @endif

    <script>
        // Track current active states of modal
        let activeTab = 'login'; // login, signup
        let activeRole = 'admin'; // admin, employee

        function openAuthModal(tab = 'login') {
            const modal = document.getElementById('auth-modal');
            const card = document.getElementById('auth-modal-card');

            modal.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }, 10);

            switchTab(tab);
        }

        function closeAuthModal() {
            const modal = document.getElementById('auth-modal');
            const card = document.getElementById('auth-modal-card');

            card.classList.remove('scale-100');
            card.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }

        function switchTab(tab) {
            activeTab = tab;
            const loginForm = document.getElementById('login-form');
            const signupForm = document.getElementById('signup-form');
            const loginBtn = document.getElementById('tab-login-btn');
            const signupBtn = document.getElementById('tab-signup-btn');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                signupForm.classList.add('hidden');

                loginBtn.classList.add('border-black', 'text-black');
                loginBtn.classList.remove('border-transparent', 'text-slate-500');
                signupBtn.classList.add('border-transparent', 'text-slate-500');
                signupBtn.classList.remove('border-black', 'text-black');
            } else {
                loginForm.classList.add('hidden');
                signupForm.classList.remove('hidden');

                signupBtn.classList.add('border-black', 'text-black');
                signupBtn.classList.remove('border-transparent', 'text-slate-500');
                loginBtn.classList.add('border-transparent', 'text-slate-500');
                loginBtn.classList.remove('border-black', 'text-black');
            }
        }

        function switchRole(role) {
            activeRole = role;

            // Sync hidden inputs for forms submission
            const loginRoleInput = document.getElementById('login-role-input');
            const signupRoleInput = document.getElementById('signup-role-input');
            if (loginRoleInput) loginRoleInput.value = role;
            if (signupRoleInput) signupRoleInput.value = role;

            const adminBtn = document.getElementById('role-admin-btn');
            const employeeBtn = document.getElementById('role-employee-btn');
            const companyNameField = document.getElementById('company-name-field');
            const companyNameInput = document.getElementById('company_name_input');
            const companyDeptField = document.getElementById('company-departments-field');
            const companyDeptInput = document.getElementById('company_departments_input');
            const companyCurrencyField = document.getElementById('company-currency-field');
            const companyCurrencyInput = document.getElementById('company_currency_input');
            const signupTip = document.getElementById('signup-tip');

            if (role === 'admin') {
                adminBtn.className = "py-2.5 rounded-none text-[10px] font-bold uppercase tracking-wider text-white bg-black border border-black transition-all text-center shadow-sm";
                employeeBtn.className = "py-2.5 rounded-none text-[10px] font-bold uppercase tracking-wider text-slate-550 hover:text-black transition-all text-center";

                if (companyNameField) companyNameField.classList.remove('hidden');
                if (companyNameInput) {
                    companyNameInput.required = true;
                    companyNameInput.disabled = false;
                }

                if (companyDeptField) companyDeptField.classList.remove('hidden');
                if (companyDeptInput) {
                    companyDeptInput.required = true;
                    companyDeptInput.disabled = false;
                }

                if (companyCurrencyField) companyCurrencyField.classList.remove('hidden');
                if (companyCurrencyInput) {
                    companyCurrencyInput.required = true;
                    companyCurrencyInput.disabled = false;
                }

                signupTip.innerHTML = `
                    <i class="fa-solid fa-shield-halved mt-0.5 text-black font-bold"></i>
                    <span>Registering as Admin (Boss) automatically provisions a brand new company payment workspace instantly.</span>
                `;
            } else {
                employeeBtn.className = "py-2.5 rounded-none text-[10px] font-bold uppercase tracking-wider text-white bg-black border border-black transition-all text-center shadow-sm";
                adminBtn.className = "py-2.5 rounded-none text-[10px] font-bold uppercase tracking-wider text-slate-550 hover:text-black transition-all text-center";

                if (companyNameField) companyNameField.classList.add('hidden');
                if (companyNameInput) {
                    companyNameInput.required = false;
                    companyNameInput.disabled = true;
                }

                if (companyDeptField) companyDeptField.classList.add('hidden');
                if (companyDeptInput) {
                    companyDeptInput.required = false;
                    companyDeptInput.disabled = true;
                }

                if (companyCurrencyField) companyCurrencyField.classList.add('hidden');
                if (companyCurrencyInput) {
                    companyCurrencyInput.required = false;
                    companyCurrencyInput.disabled = true;
                }

                signupTip.innerHTML = `
                    <i class="fa-solid fa-circle-info mt-0.5 text-black font-bold"></i>
                    <span>Registering as Employee automatically creates your profile in the directory and binds your self-service stubs.</span>
                `;
            }
        }

        // Global Alert Auto-Dismiss & Dynamic Open
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Lenis Kinetic Smooth Scrolling
            if (typeof Lenis !== 'undefined') {
                const lenis = new Lenis({
                    duration: 1.2,
                    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                    smoothWheel: true,
                    smoothTouch: false,
                });

                function raf(time) {
                    lenis.raf(time);
                    requestAnimationFrame(raf);
                }
                requestAnimationFrame(raf);

                // Smooth Scroll Interceptor for Anchor Hashes using Lenis
                document.addEventListener('click', function(e) {
                    const anchor = e.target.closest('a');
                    if (!anchor) return;
                    
                    const href = anchor.getAttribute('href');
                    if (href && href.startsWith('#') && href !== '#') {
                        const target = document.querySelector(href);
                        if (target) {
                            e.preventDefault();
                            lenis.scrollTo(target, {
                                offset: -96, // offset for sticky navigation header
                                duration: 1.2
                            });
                        }
                    }
                });
            }

            const successAlert = document.getElementById('toast-success');
            const errorAlert = document.getElementById('toast-error');

            if (successAlert) {
                setTimeout(() => {
                    successAlert.classList.add('opacity-0', 'translate-y-4');
                    setTimeout(() => successAlert.remove(), 500);
                }, 5000);
            }

            if (errorAlert) {
                openAuthModal();
                setTimeout(() => {
                    errorAlert.classList.add('opacity-0', 'translate-y-4');
                    setTimeout(() => errorAlert.remove(), 500);
                }, 5000);
            }
        });
    </script>
</body>

</html>