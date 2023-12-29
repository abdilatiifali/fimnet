@extends('layouts.master')
@include('components.header')
@section('content')

	<div class="flex  min-h-full flex-col justify-center pt-24 pb-32 sm:px-6 lg:px-8">
		<div class="sm:mx-auto sm:w-full sm:max-w-md">
			<h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
				Update Your Package
			</h2>
		</div>

	<div class="mt-10 px-4 sm:mx-auto sm:w-full sm:max-w-[480px]">
	<div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
	  <form class="space-y-6" action="/services" method="POST">
	  	@csrf
	    <div>
	      <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
	      	Your Current Package is 
	      </label>
	      <div class="mt-2">
	        <input disabled readonly 
	        value="{{ $customer->package->name }} {{ $customer->package->speed }} At {{ number_format($customer->package->price) }}KES" type="text" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
	      </div>
	    </div>

	    <div>
		  <label for="location" class="block text-sm font-medium leading-6 text-gray-900">
		  	Choose New Package
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

	<p class="mt-10 text-center text-sm text-gray-500">
	  <a href="/" class="font-semibold leading-6 text-rose-600 hover:text-rose-500">
	  	Go Back
	  </a>
	</p>
	</div>
	</div>

@endsection
