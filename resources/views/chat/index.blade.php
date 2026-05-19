@extends('layouts.app')

@section('title', 'Company Broadcast Room - PayFlow')

@section('content')
<div class="h-[calc(100vh-10rem)] flex flex-col border-2 border-black bg-white shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
    
    <!-- Active Chat Header -->
    <div class="p-4 border-b-2 border-black bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 flex-shrink-0 select-none">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center font-black text-sm bg-amber-400 border border-black shadow-sm flex-shrink-0">
                📢
            </div>
            <div>
                <h4 class="text-sm font-black text-black leading-tight flex items-center gap-2">
                    Company Broadcast Bulletin
                    <span class="inline-block w-2 h-2 bg-emerald-500 border border-black rounded-none animate-pulse" title="Channel Active"></span>
                </h4>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mt-0.5">
                    Unified company-wide communication channel for {{ auth()->user()->company_name }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-block px-3 py-1.5 bg-[#F4ECE6] border border-black text-black text-[9px] font-black uppercase tracking-widest">
                <i class="fa-solid fa-users text-amber-600 mr-1 animate-pulse"></i> {{ auth()->user()->company_name }} Group Chat
            </span>
        </div>
    </div>

    <!-- Messages Stream Container -->
    <div id="messages-container" class="flex-1 overflow-y-auto p-5 bg-[#F4ECE6] space-y-4 scroll-smooth">
        @forelse($messages as $msg)
            @php
                $isMe = $msg->sender_id === auth()->id();
            @endphp
            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} w-full">
                <div class="max-w-[75%] sm:max-w-[60%] flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} gap-1">
                    
                    <!-- Sender Name and Role Label (always show for others) -->
                    @if(!$isMe)
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-650 px-1 mb-0.5">
                            {{ $msg->sender->name }} ({{ $msg->sender->role === 'admin' ? 'Admin' : 'Employee' }})
                        </span>
                    @endif

                    <!-- Bubble Message Box -->
                    <div class="p-4 border border-black text-xs font-semibold leading-relaxed shadow-[2.5px_2.5px_0px_0px_#000] relative {{ $isMe ? 'bg-black text-white' : 'bg-white text-black' }}">
                        <p class="break-words white-space-pre-wrap">{{ $msg->message }}</p>
                    </div>
                    
                    <!-- Timestamp -->
                    <span class="text-[8px] font-extrabold uppercase tracking-wider text-slate-500 px-1 mt-0.5">
                        {{ $msg->created_at->format('M d, g:i A') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="h-full flex flex-col items-center justify-center py-12 text-center text-xs text-slate-455 font-bold uppercase tracking-wider">
                <i class="fa-solid fa-bullhorn text-4xl text-slate-350 mb-3 block animate-bounce"></i>
                No announcements recorded.
                <p class="text-[9px] text-slate-500 font-bold tracking-normal normal-case mt-1.5">Write a message below to broadcast to all company members.</p>
            </div>
        @endforelse
    </div>

    <!-- Message Input Area -->
    @if(auth()->user()->role === 'admin')
        <div class="p-4 border-t-2 border-black bg-white flex-shrink-0">
            <form action="{{ route('chat.store') }}" method="POST" id="chat-send-form" class="flex items-center gap-3">
                @csrf
                
                <div class="flex-1 border border-black px-4 py-3 bg-[#F4ECE6] shadow-[2.5px_2.5px_0px_0px_#000] focus-within:shadow-[4px_4px_0px_0px_#000] transition-all flex items-center">
                    <input type="text" name="message" placeholder="Broadcast an announcement to everyone in {{ auth()->user()->company_name }}..." required autocomplete="off"
                        class="bg-transparent border-0 outline-none text-xs w-full text-black placeholder:text-slate-455 font-semibold">
                </div>
                
                <button type="submit"
                    class="px-5 py-3 border border-black font-black text-xs uppercase tracking-wider text-white bg-black hover:bg-neutral-900 shadow-[3px_3px_0px_0px_#22c55e] transition-all active:translate-x-[2px] active:translate-y-[2px] active:shadow-none flex items-center gap-2 cursor-pointer whitespace-nowrap">
                    <i class="fa-solid fa-bullhorn text-amber-400"></i> Broadcast
                </button>
            </form>
        </div>
    @else
        <div class="p-4 border-t-2 border-black bg-[#F4ECE6] flex-shrink-0 flex items-center justify-center select-none">
            <div class="bg-white border border-black px-6 py-3.5 shadow-[3px_3px_0px_0px_#000] flex items-center gap-3">
                <i class="fa-solid fa-lock text-slate-500 animate-pulse"></i>
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-600">
                    Read-Only Workspace Channel • Direct announcements from company administration
                </span>
            </div>
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto scroll messages container to bottom on load
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }

        // Prevent double submit on chat form
        const form = document.getElementById('chat-send-form');
        if (form) {
            form.addEventListener('submit', function () {
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin"></i> Sending...';
                }
            });
        }

        // Save total broadcast count to localStorage to clear dropdown badges immediately
        const company = "{{ auth()->user()->company_name }}";
        const totalBroadcasts = {{ $messages->where('sender_id', '!=', auth()->id())->count() }};
        const storageKey = 'last_seen_broadcast_count_' + encodeURIComponent(company);
        localStorage.setItem(storageKey, totalBroadcasts.toString());
    });
</script>
@endsection
