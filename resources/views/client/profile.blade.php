@extends('layouts.master')
@include('components.header')
@section('content')

	<div class="flex  min-h-full flex-col justify-center pt-24 pb-32 sm:px-6 lg:px-8">
	<div class="sm:mx-auto sm:w-full sm:max-w-md">
	<h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
		Update Your Profile
	</h2>
	</div>

	<div class="mt-10 px-4 sm:mx-auto sm:w-full sm:max-w-[480px]">
	<div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
	  <form class="space-y-6" action="/profile" method="POST">
	  	@csrf
	    <div>
	      <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
	      	Your Name
	      </label>
	      <div class="mt-2">
	        <input id="name" value="{{ $customer->name }}" name="name" type="text" autocomplete="name" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
	      </div>
	    </div>

	    <div>
	      <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
	      	Your Phone Number
	      </label>
	      <div class="mt-2">
	        <input id="phone_number" value="{{ $customer->phone_number }}" name="phone_number" type="number" autocomplete="phone_number" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
	      </div>
	    </div>

	    <div>
	      <label for="password" class="block text-sm font-medium leading-6 text-gray-900">New Password</label>
	      <div class="mt-2">
	        <input id="password" name="password" type="password" autocomplete="current-password" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
	      </div>
	    </div>

	    <div>
	      <button type="submit" class="flex w-full justify-center rounded-md bg-rose-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-rose-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600">
	      	Update Profile
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
