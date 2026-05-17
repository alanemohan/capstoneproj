@extends('layouts.student')
@php use Illuminate\Support\Str; @endphp

@section('title', 'My Cart — Nabha Learning')

@section('student-content')
<div class="space-y-6 animate-fade-in" x-data="{ payModal: false }">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-white/40">
        <a href="{{ route('student.courses') }}" class="hover:text-violet-400 transition">{{ __('messages.courses') }}</a>
        <span class="text-white/20">›</span>
        <span class="text-white/80 font-medium">{{ __('messages.cart') }}</span>
    </nav>

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-white/90 tracking-tight" style="font-family: var(--font-display);">🛒 {{ __('messages.cart') }}
            @if($cartItems->isNotEmpty())
                <span class="text-xs font-normal text-white/40 ml-2">({{ $cartItems->count() }} {{ Str::plural('course', $cartItems->count()) }})</span>
            @endif
        </h1>
        @if($cartItems->isNotEmpty())
            <a href="{{ route('student.courses') }}" class="text-xs text-violet-400 hover:text-violet-300 font-semibold transition">+ {{ __('Add more courses') }}</a>
        @endif
    </div>

    @if($cartItems->isEmpty())
        {{-- Empty state --}}
        <div class="glass-card p-16 text-center glow-violet">
            <div class="text-5xl mb-4">🛒</div>
            <h2 class="text-lg font-bold text-white/90 mb-2" style="font-family: var(--font-display);">{{ __('Your cart is empty') }}</h2>
            <p class="text-white/40 text-xs mb-6 max-w-sm mx-auto leading-relaxed">{{ __('Browse our catalog and add courses you would like to learn.') }}</p>
            <a href="{{ route('student.courses') }}"
               class="inline-block bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-lg shadow-violet-500/20 hover:shadow-violet-500/30 hover:-translate-y-0.5 active:translate-y-0">
                {{ __('Browse Courses') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Cart items list ── --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    @php $course = $item->course; @endphp
                    <div class="glass-card p-5 flex gap-4 hover:border-violet-500/20 transition-all duration-300 relative group overflow-hidden">
                        {{-- Thumbnail --}}
                        <a href="{{ route('student.courses.show', $course) }}"
                           class="flex-shrink-0 w-28 h-20 rounded-xl overflow-hidden block relative">
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        </a>

                        <div class="flex-1 min-w-0">
                            <a href="{{ route('student.courses.show', $course) }}"
                               class="font-bold text-white/90 hover:text-violet-400 transition text-sm leading-snug block truncate" style="font-family: var(--font-display);">
                                {{ $course->title }}
                            </a>
                            <p class="text-xs text-white/40 mt-1 truncate">
                                by {{ $course->teacher->name }}
                                &middot; {{ $course->lessons_count }} {{ Str::plural('lesson', $course->lessons_count) }}
                                &middot; {{ $course->class_level }}
                            </p>
                            <div class="flex items-center gap-1 mt-2">
                                <span class="text-[9px] bg-violet-500/15 text-violet-300 px-2 py-0.5 rounded-md font-bold uppercase tracking-wider">{{ $course->subject }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col items-end justify-between flex-shrink-0">
                            <p class="text-base font-extrabold text-white/90">₹{{ number_format($course->price, 2) }}</p>
                            <form method="POST" action="{{ route('student.cart.remove', $course) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-[10px] text-red-400 hover:text-red-300 transition font-bold uppercase tracking-wider">
                                    {{ __('Remove') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── Order summary + checkout ── --}}
            <div class="lg:col-span-1">
                <div class="glass-card p-6 sticky top-20 space-y-5 glow-violet">
                    <h2 class="font-bold text-white/90 text-sm uppercase tracking-wider" style="font-family: var(--font-display);">{{ __('Order Summary') }}</h2>

                    <div class="space-y-2 text-xs">
                        @foreach($cartItems as $item)
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-white/50 truncate flex-1">{{ Str::limit($item->course->title, 32) }}</span>
                                <span class="text-white/85 font-semibold flex-shrink-0">₹{{ number_format($item->course->price, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-white/[0.06] pt-4 flex items-center justify-between">
                        <span class="font-bold text-white/50 text-xs uppercase tracking-wider">Total</span>
                        <span class="text-xl font-extrabold text-white/90">₹{{ number_format($total, 2) }}</span>
                    </div>

                    <button @click="payModal = true"
                            class="block w-full text-center bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-lg shadow-violet-500/20 hover:shadow-violet-500/30 hover:-translate-y-0.5 active:translate-y-0">
                        🔒 Checkout ({{ $cartItems->count() }} {{ Str::plural('course', $cartItems->count()) }})
                    </button>

                    <div class="bg-amber-500/10 border border-amber-500/15 rounded-xl p-3 text-[11px] text-amber-300 flex gap-2 leading-relaxed">
                        <span class="flex-shrink-0">⚠️</span>
                        <span><strong>{{ __('Demo only.') }}</strong> {{ __('No real payment is processed.') }}</span>
                    </div>

                    <p class="text-center text-[10px] text-white/35">🔒 Safe & Secure Simulation</p>
                </div>
            </div>

        </div>
    @endif

    {{-- ── Payment Modal ── --}}
    @if($cartItems->isNotEmpty())
    <div x-show="payModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-md" @click="payModal = false"></div>

        <div class="relative glass-card border border-white/[0.08] p-0 w-full max-w-md z-10 glow-violet overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between px-6 py-4 border-b border-white/[0.06]">
                <div>
                    <h3 class="font-bold text-white/95 text-sm uppercase tracking-wider" style="font-family: var(--font-display);">Complete Purchase</h3>
                    <p class="text-[10px] text-white/40 mt-0.5">
                        {{ $cartItems->count() }} {{ Str::plural('course', $cartItems->count()) }}
                        &middot; Total ₹{{ number_format($total, 2) }}
                    </p>
                </div>
                <button @click="payModal = false" class="text-white/40 hover:text-white/70 transition p-1.5 rounded-lg hover:bg-white/[0.04]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mini course list inside modal --}}
            <div class="mx-6 mt-4 bg-white/[0.02] border border-white/[0.06] rounded-xl p-3.5 space-y-1.5 max-h-32 overflow-y-auto custom-scrollbar">
                @foreach($cartItems as $item)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-white/60 truncate flex-1 pr-2">{{ Str::limit($item->course->title, 36) }}</span>
                        <span class="font-bold text-white/85 flex-shrink-0">₹{{ number_format($item->course->price, 2) }}</span>
                    </div>
                @endforeach
                <div class="border-t border-white/[0.06] pt-1.5 flex items-center justify-between text-xs font-bold">
                    <span class="text-white/50">Total</span>
                    <span class="text-white/90 text-sm">₹{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('student.cart.checkout') }}"
                  class="px-6 pb-6 pt-4 space-y-4"
                  x-data="paymentForm()"
                  @submit="loading = true">
                @csrf

                {{-- Payment method selector --}}
                <div>
                    <p class="text-[10px] font-bold text-white/40 mb-2 uppercase tracking-wider">Payment Method</p>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-2.5 border rounded-xl px-4 py-3.5 cursor-pointer transition"
                               :class="method === 'card' ? 'border-violet-500/50 bg-violet-500/10 text-violet-300' : 'border-white/[0.08] hover:border-white/[0.12] text-white/50'">
                            <input type="radio" name="payment_method" value="card" x-model="method" class="accent-violet-500">
                            <span class="text-sm font-bold uppercase tracking-wider">Card</span>
                        </label>
                        <label class="flex items-center justify-center gap-2.5 border rounded-xl px-4 py-3.5 cursor-pointer transition"
                               :class="method === 'upi' ? 'border-violet-500/50 bg-violet-500/10 text-violet-300' : 'border-white/[0.08] hover:border-white/[0.12] text-white/50'">
                            <input type="radio" name="payment_method" value="upi" x-model="method" class="accent-violet-500">
                            <span class="text-sm font-bold uppercase tracking-wider">UPI</span>
                        </label>
                    </div>
                </div>

                {{-- Card fields --}}
                <div x-show="method === 'card'" x-transition class="space-y-3.5">
                    <div>
                        <label class="block text-[10px] font-semibold text-white/50 mb-1.5 uppercase tracking-wider">Cardholder Name</label>
                        <input type="text" name="card_name" x-model="name"
                               placeholder="Name on card"
                               class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-white/50 mb-1.5 uppercase tracking-wider">Card Number</label>
                        <div class="relative">
                            <input type="text" x-model="cardDisplay"
                                   @input="formatCard($event)"
                                   placeholder="1234 5678 9012 3456" maxlength="19"
                                   class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 pr-12 tracking-widest transition text-xs">
                            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-white/30 text-base">💳</div>
                        </div>
                        <input type="hidden" name="card_number" :value="cardRaw">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-white/50 mb-1.5 uppercase tracking-wider">Expiry</label>
                            <input type="text" name="card_expiry" @input="formatExpiry($event)"
                                   placeholder="MM/YY" maxlength="5"
                                   class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 tracking-widest transition text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-white/50 mb-1.5 uppercase tracking-wider">CVV</label>
                            <input type="text" name="card_cvv" placeholder="•••" maxlength="4"
                                   class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 tracking-widest transition text-xs">
                        </div>
                    </div>
                </div>

                {{-- UPI section --}}
                <div x-show="method === 'upi'" x-transition class="space-y-3">
                    <div class="flex flex-col items-center gap-3 py-2">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data=upi://pay?pa=8247592083@axl%26am={{ $total }}%26cu=INR&size=180x180&bgcolor=ffffff&color=4f46e5&margin=8"
                             alt="UPI QR Code"
                             class="w-44 h-44 rounded-2xl border border-white/[0.08] shadow-md p-1.5 bg-white">
                        <div class="text-center">
                            <p class="text-[10px] text-white/40 mb-0.5">Scan to Pay</p>
                            <p class="text-lg font-extrabold text-violet-400">₹{{ number_format($total, 2) }}</p>
                        </div>
                        <div class="bg-white/[0.02] border border-white/[0.06] rounded-xl px-5 py-3 text-center w-full">
                            <p class="text-[10px] text-white/40 mb-0.5">UPI ID</p>
                            <p class="text-xs font-bold text-violet-300 tracking-wide select-all">8247592083@axl</p>
                        </div>
                    </div>
                    <input type="hidden" name="upi_id" value="8247592083@axl">
                    <div class="bg-blue-500/10 border border-blue-500/15 rounded-xl p-3 text-[11px] text-blue-300 leading-relaxed">
                        Scan the QR code or use the UPI ID above to complete payment. This is a simulated demo — no real money is charged.
                    </div>
                </div>

                <div class="bg-amber-500/10 border border-amber-500/15 rounded-xl p-3 text-[11px] text-amber-300 flex gap-2 leading-relaxed">
                    <span class="flex-shrink-0">⚠️</span>
                    <span><strong>Demo only.</strong> No real money is charged.</span>
                </div>

                <button type="submit" :disabled="loading"
                        class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed text-white py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-lg shadow-violet-500/20">
                    <svg x-show="loading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-show="!loading" x-text="method === 'upi' ? '📱 Pay via UPI & Enroll All' : '🔒 Pay ₹{{ number_format($total, 2) }} & Enroll All'"></span>
                    <span x-show="loading" x-cloak>Processing...</span>
                </button>

                <p class="text-center text-[10px] text-white/35">🔒 Secured simulation</p>
            </form>
        </div>
    </div>
    @endif

</div>

@if($errors->hasAny(['card_number','card_expiry','card_cvv','card_name','upi_id','payment_method']))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.querySelector('[x-data]');
            if (root && root._x_dataStack) root._x_dataStack[0].payModal = true;
        });
    </script>
    @endpush
@endif

@push('scripts')
<script>
function paymentForm() {
    return {
        method: '{{ old("payment_method", "card") }}',
        loading: false,
        name: '',
        cardDisplay: '',
        cardRaw: '',
        formatCard(e) {
            const digits = e.target.value.replace(/\D/g, '').slice(0, 16);
            this.cardRaw     = digits;
            this.cardDisplay = digits.replace(/(.{4})/g, '$1 ').trim();
            e.target.value   = this.cardDisplay;
        },
        formatExpiry(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 4);
            if (v.length >= 3) v = v.slice(0, 2) + '/' + v.slice(2);
            e.target.value = v;
        },
    };
}
</script>
@endpush
@endsection
