@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="bg-indigo-600 p-6 text-white text-center">
            <div class="flex justify-center mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold">Secure Payment Gateway</h1>
            <p class="text-indigo-100 mt-1">Simulated Transaction</p>
        </div>

        <div class="p-8">
            <div class="mb-6">
                <h2 class="text-gray-600 text-sm font-semibold uppercase tracking-wider mb-2">Order Summary</h2>
                <div class="space-y-3">
                    @foreach($enrollments as $enrollment)
                    <div class="flex justify-between items-center text-gray-800">
                        <span class="text-sm font-medium">{{ $enrollment->course->title }}</span>
                        <span class="font-bold">₹{{ number_format($enrollment->amount_paid, 2) }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between items-center text-lg font-bold text-gray-900">
                    <span>Total Amount</span>
                    <span class="text-indigo-600">₹{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            This is a <span class="font-bold">mock gateway</span>. No actual money will be deducted. Select an outcome to proceed.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('student.payment.callback') }}" method="POST" id="paymentForm" class="space-y-4">
                @csrf
                <input type="hidden" name="status" id="paymentStatus" value="fail">
                @foreach($enrollments as $enrollment)
                    <input type="hidden" name="enrollments[]" value="{{ $enrollment->id }}">
                @endforeach

                <button type="button" onclick="submitPayment('success')"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simulate Successful Payment
                </button>

                <button type="button" onclick="submitPayment('fail')"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Simulate Payment Failure
                </button>
            </form>

            <script>
                function submitPayment(status) {
                    document.getElementById('paymentStatus').value = status;
                    document.getElementById('paymentForm').submit();
                }
            </script>
        </div>

        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <p class="text-xs text-gray-400">Payment ID: NABHA-{{ strtoupper(\Illuminate\Support\Str::random(8)) }}</p>
        </div>
    </div>
</div>
@endsection
