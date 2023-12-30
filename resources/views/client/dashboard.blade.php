@extends('layouts.master')
@include('components.header')
@section('content')
  <main>
    <div class="relative isolate overflow-hidden pt-16">
      <!-- Stats -->
      <div class="border-b border-b-gray-900/10 lg:border-t lg:border-t-gray-900/5">
        <dl class="mx-auto grid max-w-7xl grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 lg:px-2 xl:px-0">
          <div class="flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8">
            <dt class="text-sm font-medium leading-6 text-gray-500">Package: {{ $package->speed }}</dt>
            <dd class="text-xs font-medium text-gray-700">
              {{ $package->name }}
            </dd>
            <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
              {{ number_format($package->price, 2) }}KES
            </dd>
          </div>
          @if ($customer->balance() > 0)
            <div class="flex items-baseline flex-wrap justify-between gap-y-2 gap-x-4 border-t border-gray-900/5 px-4 py-10 sm:px-6 lg:border-t-0 xl:px-8 sm:border-l">
              <dt class="text-sm font-medium leading-6 text-gray-500">Overdue invoices</dt>
              <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
                {{ number_format($customer->balance(), 2) }}KES
              </dd>
            </div>
          @endif
        </dl>
      </div>

      <div class="absolute left-0 top-full -z-10 mt-96 origin-top-left translate-y-40 -rotate-90 transform-gpu opacity-20 blur-3xl sm:left-1/2 sm:-ml-96 sm:-mt-10 sm:translate-y-0 sm:rotate-0 sm:transform-gpu sm:opacity-50" aria-hidden="true">
        <div class="aspect-[1154/678] w-[72.125rem] bg-gradient-to-br from-[#FF80B5] to-[#9089FC]" style="clip-path: polygon(100% 38.5%, 82.6% 100%, 60.2% 37.7%, 52.4% 32.1%, 47.5% 41.8%, 45.2% 65.6%, 27.5% 23.4%, 0.1% 35.3%, 17.9% 0%, 27.7% 23.4%, 76.2% 2.5%, 74.2% 56%, 100% 38.5%)"></div>
      </div>
    </div>

    <div class="space-y-16 py-16 xl:space-y-20">
      <!-- Recent activity table -->
      <div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between">
              @if ($customer->balance() > 0)
                  <a href="/payments" class="ml-auto flex items-center gap-x-1 rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                  Pay Now
                </a>
              @endif
              <div class="ml-4">
                <a href="/statement/{{ $customer->id }}" class="block text-red-500">
                  Download Statement
                </a>
              </div>
          </div>
          <div class="mt-16">
            <h2 class="mx-auto max-w-2xl text-base font-semibold leading-6 text-gray-900 lg:mx-0 lg:max-w-none">Recent Transaction</h2>
          </div>
        </div>
        <div class="mt-6 overflow-hidden border-t border-gray-100">
          <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-none">
              <table class="w-full text-left">
                <thead class="sr-only">
                  <tr>
                    <th>Amount</th>
                    <th class="hidden sm:table-cell">Client</th>
                    <th>More details</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($subscriptions as $subscription)
                    <tr>
                      <td class="py-5 pr-6 sm:table-cell">
                        <div class="flex gap-x-1">
                          <svg class="hidden h-6 w-5 flex-none text-gray-400 sm:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-4.75a.75.75 0 001.5 0V8.66l1.95 2.1a.75.75 0 101.1-1.02l-3.25-3.5a.75.75 0 00-1.1 0L6.2 9.74a.75.75 0 101.1 1.02l1.95-2.1v4.59z" clip-rule="evenodd" />
                            </svg>
                          <div class="text-sm leading-6 text-gray-900">
                            {{ \App\Enums\Month::from($subscription->month_id)->name }}
                          </div>
                        </div>
                      </td>

                      <td class="relative py-5 pr-6">
                        <div class="flex gap-x-6">
                          <div class="flex-auto">
                            <div class="flex items-start gap-x-3">
                              <div class="text-sm font-medium leading-6 text-gray-900">
                                {{ number_format($subscription->amount, 2) }}
                              </div>
                              @if ($subscription->paid)
                                @if ($subscription->payment_type == 'mpesa')
                                  <div class="rounded-md py-1 px-2 text-xs font-medium ring-1 ring-inset text-green-700 bg-green-50 ring-green-600/20">MPESA</div>
                                @else
                                   <div class="rounded-md py-1 px-2 text-xs font-medium ring-1 ring-inset text-indigo-700 bg-indigo-50 ring-indigo-600/20">CASH</div>
                                @endif
                              @else
                                <div class="rounded-md py-1 px-2 text-xs font-medium ring-1 ring-inset text-red-700 bg-red-50 ring-red-600/10">Overdue</div>
                              @endif
                            </div>
                          </div>
                        </div>
                        <div class="absolute bottom-0 right-full h-px w-screen bg-gray-100"></div>
                        <div class="absolute bottom-0 left-0 h-px w-screen bg-gray-100"></div>
                      </td>
                      <td  class="py-5 text-right">
                        
                        <div class="mt-1 text-xs leading-5 text-gray-500">Invoice <span class="text-gray-900">#000{{ rand(1, 100) }}</span></div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

@endsection

