@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl text-center">
        <div>
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-yellow-100 mb-6">
                <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">
                {{ auth()->user()->status === 'rejected' ? 'Account Rejected' : 'Approval Pending' }}
            </h2>
            <p class="mt-4 text-sm text-gray-600">
                {{ session('status_message') ?? (auth()->user()->status === 'rejected' ? 'Your account has been rejected. Please contact the administrator for more information.' : 'Your account is currently under review by our administration team. You will be able to access the portal once your account is approved.') }}
            </p>
        </div>
        <div class="mt-8">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
