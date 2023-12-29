@extends('layouts.master')
@section('content')
  <div class="flex min-h-full flex-col justify-center pt-8 pb-16 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full pt-8 sm:max-w-md">
      <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
        Pay Via Mpesa
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
      <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
        <form class="space-y-6" action="#" method="POST">
          <div>
            <label for="your-phone-number" class="block text-sm font-medium leading-6 text-gray-900">
              Your Registerd Phone Number
            </label>
            <div class="mt-2">
              <input value="{{ $customer->phone_number }}" id="your-phone-number" readonly name="your-phone-number" type="your-phone-number" autocomplete="your-phone-number" required class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div>
            <label for="amount" class="block text-sm font-medium leading-6 text-gray-900">amount</label>
            <div class="mt-2">
              <input id="amount" name="amount" value="{{ $customer->balance() }}" readonly type="amount" autocomplete="current-amount" required class="px-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div>
            <button type="submit" class="flex w-full justify-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
              Pay via Mpesa
            </button>
          </div>
        </form>

        <div>
          <div class="relative mt-10">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
              <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm font-medium leading-6">
              <span class="bg-white px-6 text-gray-900">Instruction To pay</span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-1 gap-4">
            <ul role="list" class="mt-4 space-y-3 text-sm leading-6 text-gray-600">
              <li class="flex gap-x-3">
                <svg class="h-6 w-5 flex-none text-rose-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                </svg>
                Your will Recieve Prompt in your phone Number.
              </li>
              <li class="flex gap-x-3">
                <svg class="h-6 w-5 flex-none text-rose-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                </svg>
                Enter Pin to Authroize Payment.
              </li>
              <li class="flex gap-x-3">
                <svg class="h-6 w-5 flex-none text-rose-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                </svg>
                Your will be recieve notification to confirm your payment.
              </li>
            </ul>
          </div>
        </div>
      </div>

      <p class="mt-10 text-center text-sm text-gray-500">
        Changed Phone Number?
        <a href="/profile" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
          Go to your profile page and update it there.
        </a>
      </p>
    </div>
  </div>
@endsection
