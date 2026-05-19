@extends('layouts.app')

@section('title', 'Daily Attendance - PayFlow')

@section('content')
<div class="space-y-8">
    <!-- Header Block -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tight text-black flex items-center gap-2">
                <i class="fa-solid fa-id-card text-black"></i> Daily Attendance
            </h1>
            <p class="text-xs text-slate-650 mt-1.5 font-bold uppercase tracking-wider">Live tracking log for RFID card sweeps and terminal check-ins.</p>
        </div>

        <!-- Toggle Segmented Control -->
        <div class="inline-flex border-2 border-black p-1 bg-[#F4ECE6] shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] select-none">
            <button type="button" id="btn-live-mode" onclick="setMode('live')"
                class="px-4 py-2 font-black text-[10px] uppercase tracking-widest transition-all bg-black text-white border border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,0.15)] cursor-pointer">
                <i class="fa-solid fa-satellite-dish mr-1"></i> RFID Live Feed
            </button>
            <button type="button" id="btn-manual-mode" onclick="setMode('manual')"
                class="px-4 py-2 font-black text-[10px] uppercase tracking-widest transition-all text-black hover:bg-black/5 border border-transparent cursor-pointer">
                <i class="fa-solid fa-keyboard mr-1"></i> Manual Override
            </button>
        </div>
    </div>

    <!-- Info Status Box -->
    <div class="bg-[#F4ECE6] border-2 border-black p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-all duration-300">
        <div class="space-y-1">
            <div id="status-live-info" class="space-y-1">
                <h4 class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 bg-emerald-500 border border-black inline-block animate-pulse"></span> RFID Terminal Sync Active
                </h4>
                <p class="text-slate-600 text-xs font-semibold leading-relaxed">
                    Attendance logs are currently synced with gate RFID swipes. Switch to Manual Override to make updates.
                </p>
            </div>
            <div id="status-manual-info" class="space-y-1 hidden">
                <h4 class="text-xs font-black uppercase tracking-wider text-amber-800 flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 bg-amber-500 border border-black inline-block animate-bounce"></span> Manual Roster Override Active
                </h4>
                <p class="text-slate-600 text-xs font-semibold leading-relaxed">
                    You can now manually alter attendance states. Click "Save Attendance Roster" below when complete.
                </p>
            </div>
        </div>
        <div class="flex-shrink-0">
            <span id="badge-live-feed" class="inline-block px-3 py-1.5 bg-black border border-black text-white text-[9px] font-black uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,0.15)]">
                Live Roster Feed
            </span>
            <span id="badge-manual-override" class="inline-block px-3 py-1.5 bg-amber-500 border border-black text-black text-[9px] font-black uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,0.15)] hidden">
                Manual Edit Mode
            </span>
        </div>
    </div>

    <!-- Date Selection Card -->
    <div class="bg-white border border-black rounded-none p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <form action="{{ route('attendance.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-none bg-black border border-black text-white flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-calendar text-base text-emerald-400"></i>
                </div>
                <div>
                    <span class="block text-[9px] font-black uppercase tracking-wider text-slate-455">Selected Roster Date</span>
                    <span class="text-black text-sm font-black uppercase tracking-wider">{{ Carbon\Carbon::parse($date)->format('F d, Y') }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <input type="date" name="date" value="{{ $date }}" max="{{ date('Y-m-d') }}"
                    class="block w-full sm:w-48 rounded-none border border-black bg-[#F4ECE6] py-2.5 px-3.5 text-black focus:ring-0 focus:border-black text-xs font-bold transition-all duration-200">
                <button type="submit"
                    class="px-5 py-2.5 rounded-none bg-black hover:bg-neutral-800 font-extrabold text-xs uppercase tracking-wider text-white border border-black transition-colors whitespace-nowrap cursor-pointer">
                    Go to Date
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance Roster Sheet -->
    <div class="bg-white border border-black rounded-none p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
        <form action="{{ route('attendance.store') }}" method="POST" id="attendance-form">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-black pb-4 mb-4 gap-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-1.5">
                        <i class="fa-solid fa-clipboard-list text-black"></i> Employee Sweep Log
                    </h3>
                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-455 bg-[#F4ECE6] px-2.5 py-1 border border-black">
                        Roster headcount: {{ count($employees) }}
                    </span>
                </div>

                <!-- Bulk Actions (Visible in Manual Mode) -->
                <div id="bulk-actions" class="hidden flex flex-wrap items-center gap-2 select-none">
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 mr-1">Bulk Apply:</span>
                    <button type="button" onclick="bulkMark('Present')" class="px-2.5 py-1.5 bg-white hover:bg-emerald-50 border border-black text-[9px] font-black uppercase tracking-wider text-emerald-800 shadow-[1px_1px_0px_0px_#000] active:translate-y-[1px] active:shadow-none transition-all cursor-pointer">
                        <i class="fa-solid fa-circle-check text-emerald-600 mr-0.5"></i> All Present
                    </button>
                    <button type="button" onclick="bulkMark('Leave')" class="px-2.5 py-1.5 bg-white hover:bg-amber-50 border border-black text-[9px] font-black uppercase tracking-wider text-amber-800 shadow-[1px_1px_0px_0px_#000] active:translate-y-[1px] active:shadow-none transition-all cursor-pointer">
                        <i class="fa-regular fa-clock text-amber-600 mr-0.5"></i> All Leave
                    </button>
                    <button type="button" onclick="bulkMark('Absent')" class="px-2.5 py-1.5 bg-white hover:bg-rose-50 border border-black text-[9px] font-black uppercase tracking-wider text-rose-800 shadow-[1px_1px_0px_0px_#000] active:translate-y-[1px] active:shadow-none transition-all cursor-pointer">
                        <i class="fa-solid fa-circle-xmark text-rose-600 mr-0.5"></i> All Absent
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="text-slate-400 text-[10px] font-black uppercase tracking-wider border-b border-black pb-3">
                            <th class="py-3.5 px-4 font-bold text-black">Employee ID</th>
                            <th class="py-3.5 px-4 font-bold text-black">Full Name</th>
                            <th class="py-3.5 px-4 font-bold text-black">Role & Department</th>
                            <th class="py-3.5 px-4 font-bold text-right" id="status-column-header">Card Sweep Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/10">
                        @forelse($employees as $emp)
                            @php
                                $marked = $markedAttendance->get($emp->id);
                                $status = $marked ? $marked->status : null;
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="py-4.5 px-4 font-mono text-xs font-bold text-black">
                                    {{ $emp->employee_id }}
                                </td>
                                <td class="py-4.5 px-4 font-black text-black text-xs">
                                    {{ $emp->name }}
                                </td>
                                <td class="py-4.5 px-4">
                                    <span class="text-xs text-slate-700 font-bold uppercase tracking-wider">{{ $emp->designation }}</span>
                                    <span class="text-[9px] text-slate-455 block font-bold uppercase tracking-widest mt-0.5">{{ $emp->department }}</span>
                                </td>
                                <td class="py-4.5 px-4 text-right">
                                    <!-- Read-only view -->
                                    <div class="live-status-container inline-block">
                                        @if($status === 'Present')
                                            <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-50 border border-emerald-500 px-3 py-1.5 rounded-none shadow-[2px_2px_0px_0px_#059669]">
                                                <i class="fa-solid fa-circle-check text-emerald-500"></i> Swiped (Present)
                                            </span>
                                        @elseif($status === 'Leave')
                                            <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-500 px-3 py-1.5 rounded-none shadow-[2px_2px_0px_0px_#D97706]">
                                                <i class="fa-regular fa-clock text-amber-500"></i> Approved Leave
                                            </span>
                                        @elseif($status === 'Absent')
                                            <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-rose-800 bg-rose-50 border border-rose-500 px-3 py-1.5 rounded-none shadow-[2px_2px_0px_0px_#E11D48]">
                                                <i class="fa-solid fa-circle-xmark text-rose-500"></i> Absent (No Swipe)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-slate-500 bg-[#F4ECE6] border border-black px-3 py-1.5 rounded-none">
                                                <i class="fa-solid fa-hourglass-start text-slate-450"></i> Awaiting Sweep...
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Interactive manual selection view -->
                                    <div class="manual-status-container hidden">
                                        <div class="inline-flex gap-2.5">
                                            <!-- Present Radio -->
                                            <label class="cursor-pointer relative flex items-center justify-center">
                                                <input type="radio" name="attendance[{{ $emp->id }}]" value="Present" class="sr-only peer" {{ $status === 'Present' ? 'checked' : '' }}>
                                                <span class="px-3.5 py-1.5 border border-black font-extrabold text-[9px] uppercase tracking-widest text-black bg-white peer-checked:bg-emerald-500 peer-checked:text-white transition-all shadow-[2px_2px_0px_0px_#000] peer-checked:shadow-none peer-checked:translate-x-[2px] peer-checked:translate-y-[2px] hover:bg-neutral-50 select-none">
                                                    <i class="fa-solid fa-circle-check text-emerald-600 peer-checked:text-white mr-1"></i> Present
                                                </span>
                                            </label>

                                            <!-- Leave Radio -->
                                            <label class="cursor-pointer relative flex items-center justify-center">
                                                <input type="radio" name="attendance[{{ $emp->id }}]" value="Leave" class="sr-only peer" {{ $status === 'Leave' ? 'checked' : '' }}>
                                                <span class="px-3.5 py-1.5 border border-black font-extrabold text-[9px] uppercase tracking-widest text-black bg-white peer-checked:bg-amber-500 peer-checked:text-white transition-all shadow-[2px_2px_0px_0px_#000] peer-checked:shadow-none peer-checked:translate-x-[2px] peer-checked:translate-y-[2px] hover:bg-neutral-50 select-none">
                                                    <i class="fa-regular fa-clock text-amber-600 peer-checked:text-white mr-1"></i> Leave
                                                </span>
                                            </label>

                                            <!-- Absent Radio -->
                                            <label class="cursor-pointer relative flex items-center justify-center">
                                                <input type="radio" name="attendance[{{ $emp->id }}]" value="Absent" class="sr-only peer" {{ $status === 'Absent' ? 'checked' : '' }}>
                                                <span class="px-3.5 py-1.5 border border-black font-extrabold text-[9px] uppercase tracking-widest text-black bg-white peer-checked:bg-rose-500 peer-checked:text-white transition-all shadow-[2px_2px_0px_0px_#000] peer-checked:shadow-none peer-checked:translate-x-[2px] peer-checked:translate-y-[2px] hover:bg-neutral-50 select-none">
                                                    <i class="fa-solid fa-circle-xmark text-rose-600 peer-checked:text-white mr-1"></i> Absent
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-xs text-slate-450 font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-user-slash text-4xl text-slate-350 mb-3 block"></i>
                                    No active employees registered. Set up some employees in the directory first.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Form Actions Row (Visible in Manual Mode) -->
            <div id="form-actions" class="hidden border-t border-black mt-6 pt-6 flex items-center justify-end gap-3 select-none">
                <button type="button" onclick="setMode('live')"
                    class="px-5 py-3 border border-black font-extrabold text-xs uppercase tracking-wider text-black bg-white hover:bg-neutral-50 shadow-[3px_3px_0px_0px_#000] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-3 border border-black font-black text-xs uppercase tracking-wider text-white bg-black hover:bg-neutral-900 shadow-[3px_3px_0px_0px_#22c55e] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-up text-emerald-400"></i> Save Attendance Roster
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setMode(mode) {
        const btnLive = document.getElementById('btn-live-mode');
        const btnManual = document.getElementById('btn-manual-mode');
        const statusLiveInfo = document.getElementById('status-live-info');
        const statusManualInfo = document.getElementById('status-manual-info');
        const badgeLiveFeed = document.getElementById('badge-live-feed');
        const badgeManualOverride = document.getElementById('badge-manual-override');
        const bulkActions = document.getElementById('bulk-actions');
        const formActions = document.getElementById('form-actions');
        const statusColumnHeader = document.getElementById('status-column-header');
        
        const liveContainers = document.querySelectorAll('.live-status-container');
        const manualContainers = document.querySelectorAll('.manual-status-container');

        if (mode === 'manual') {
            // Update mode buttons styling
            btnLive.className = "px-4 py-2 font-black text-[10px] uppercase tracking-widest transition-all text-black hover:bg-black/5 border border-transparent cursor-pointer";
            btnManual.className = "px-4 py-2 font-black text-[10px] uppercase tracking-widest transition-all bg-black text-white border border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,0.15)] cursor-pointer";

            // Update status box and badges
            statusLiveInfo.classList.add('hidden');
            statusManualInfo.classList.remove('hidden');
            badgeLiveFeed.classList.add('hidden');
            badgeManualOverride.classList.remove('hidden');
            
            // Show bulk actions and form buttons
            if (bulkActions) bulkActions.classList.remove('hidden');
            if (formActions) formActions.classList.remove('hidden');
            if (statusColumnHeader) statusColumnHeader.textContent = "Manual Roster Entry";

            // Show manual inputs, hide live badges
            liveContainers.forEach(el => el.classList.add('hidden'));
            manualContainers.forEach(el => el.classList.remove('hidden'));

            localStorage.setItem('attendance_mode', 'manual');
        } else {
            // Update mode buttons styling
            btnManual.className = "px-4 py-2 font-black text-[10px] uppercase tracking-widest transition-all text-black hover:bg-black/5 border border-transparent cursor-pointer";
            btnLive.className = "px-4 py-2 font-black text-[10px] uppercase tracking-widest transition-all bg-black text-white border border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,0.15)] cursor-pointer";

            // Update status box and badges
            statusLiveInfo.classList.remove('hidden');
            statusManualInfo.classList.add('hidden');
            badgeLiveFeed.classList.remove('hidden');
            badgeManualOverride.classList.add('hidden');
            
            // Hide bulk actions and form buttons
            if (bulkActions) bulkActions.classList.add('hidden');
            if (formActions) formActions.classList.add('hidden');
            if (statusColumnHeader) statusColumnHeader.textContent = "Card Sweep Status";

            // Hide manual inputs, show live badges
            liveContainers.forEach(el => el.classList.remove('hidden'));
            manualContainers.forEach(el => el.classList.add('hidden'));

            localStorage.setItem('attendance_mode', 'live');
        }
    }

    function bulkMark(status) {
        const inputs = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
        inputs.forEach(input => {
            input.checked = true;
        });
    }

    // Auto load the mode from localStorage
    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('attendance_mode') || 'live';
        setMode(savedMode);

        const form = document.getElementById('attendance-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // If in manual mode, make sure at least one option is marked
                const activeMode = localStorage.getItem('attendance_mode') || 'live';
                if (activeMode === 'manual') {
                    const checkedRadios = form.querySelectorAll('input[type="radio"]:checked');
                    if (checkedRadios.length === 0) {
                        e.preventDefault();
                        alert('Please mark attendance for at least one employee before saving!');
                    }
                }
            });
        }
    });
</script>
@endsection

