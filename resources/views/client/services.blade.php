@extends('layouts.master')

@section('content')
	<div class="overflow-hidden pt-24 px-12 rounded-lg bg-white shadow">
	  <h2 class="sr-only" id="profile-overview-title">Profile Overview</h2>
	  <div class="bg-white p-6">
	    <div class="sm:flex sm:items-center sm:justify-between">
	      <div class="sm:flex sm:space-x-5">
	        <div class="flex-shrink-0">
	          <img class="mx-auto h-20 w-20 rounded-full" src="{{ auth()->user()->defaultProfilePhotoUrl() }}" alt="">
	        </div>
	        <div class="mt-4 text-center sm:mt-0 sm:pt-1 sm:text-left">
	          <p class="text-sm font-medium text-gray-600">Welcome back,</p>
	          <p class="text-xl font-bold text-gray-900 sm:text-2xl">
	          	{{ $customer->name }}
	          </p>
	        </div>
	      </div>
	      <div class="mt-5 flex justify-center sm:mt-0">
	        <a href="#" class="flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">View profile</a>
	      </div>
	    </div>
	  </div>
	  <div class="grid grid-cols-1 divide-y divide-gray-200 border-t border-gray-200 bg-gray-50 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
	    <div class="px-6 py-5 text-center text-sm font-medium">
	      <span class="text-gray-600">
	      	Current Package:
	      </span>
	      <span class="text-gray-900 font-bold uppercase text-base">{{ $customer->package->name }}</span>
	    </div>
	    <div class="px-6 py-5 text-center text-sm font-medium">
	      <span class="text-gray-600">
	      	Speed:
	      </span>
	      <span class="text-gray-900 bold uppercase text-base">{{ $customer->package->speed }}</span>
	    </div>
	    <div class="px-6 py-5 text-center text-sm font-medium">
	      <span class="text-gray-600">Amount:</span>
	      <span class="text-gray-900 font-bold text-base">
	      	{{ number_format($customer->package->price, 2) }}KES
	      </span>
	    </div>
	  </div>
	</div>

	<div 
		class="flex min-h-full flex-col justify-center sm:px-6 lg:px-8"
	>
	  <div class="sm:mx-auto sm:w-full sm:max-w-md">
	    <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
	    	Upgrade Your Package
	    </h2>
	  </div>

	  <div 
	  	class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]"
	  >
	    <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
	      <form class="space-y-6" action="/services" method="POST">
	      	@csrf
	        <div>
			  <label for="location" class="block text-sm font-medium leading-6 text-gray-900">
			  	Choose Package
			  </label>
			  <select id="location" name="current-package"  class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-rose-600 sm:text-sm sm:leading-6">
			  		@foreach($packages as $package)
				    	<option value="{{ $package->id }}">
				    		{{ $package->name }}  {{ $package->speed }}MBPS AT {{ number_format($package->price) }}KES
				    	</option>
				    @endforeach
			  </select>
			</div>

	        <div>
	          <button type="submit" class="flex w-full justify-center rounded-md bg-rose-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-rose-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600">
	          	Upgrade Package
	          </button>
	        </div>
	      </form>
	    </div>
	  </div>
	</div>



@endsection
