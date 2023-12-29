<header class="absolute inset-x-0 top-0 z-50 flex h-16 border-b border-gray-900/10">
  <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
    <div class="flex flex-1 items-center gap-x-6">
      <button type="button" class="-m-3 p-3 md:hidden">
        <span class="sr-only">Open main menu</span>
        <svg class="h-5 w-5 text-gray-900" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10zm0 5.25a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
        </svg>
      </button>
      <img class="h-16 w-16" src="{{ asset('fimnet-logo.png') }}" alt="Your Company">
    </div>
    	@if (auth()->user())
      <nav class="hidden md:flex md:gap-x-11 md:text-sm md:font-semibold md:leading-6 md:text-gray-700">
        <a href="/">Home</a>
        <a href="/services">Services</a>
        <a href="/profile">Profile</a>
      </nav>
      <div class="flex flex-1 items-center justify-end gap-x-8">
      	<form method="POST" action="/logout">
      		@csrf
	        <button class="-m-1.5 p-1.5 text-rose-600">
	          <span class="sr-only">Logout</span>
	          Logout
	        </button>
	    </form>
      </div>
    @endif

  </div>
  <!-- Mobile menu, show/hide based on menu open state. -->
{{--    <div class="lg:hidden" role="dialog" aria-modal="true">
    <!-- Background backdrop, show/hide based on slide-over state. -->
    <div class="fixed inset-0 z-50"></div>
    <div class="fixed inset-y-0 left-0 z-50 w-full overflow-y-auto bg-white px-4 pb-6 sm:max-w-sm sm:px-6 sm:ring-1 sm:ring-gray-900/10">
      <div class="-ml-0.5 flex h-16 items-center gap-x-6">
        <button type="button" class="-m-2.5 p-2.5 text-gray-700">
          <span class="sr-only">Close menu</span>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <div class="-ml-0.5">
          <a href="#" class="-m-1.5 block p-1.5">
            <span class="sr-only">Your Company</span>
            <img class="h-8 w-auto" src="{{ asset('/fimnet-logo.png') }}" alt="">
          </a>
        </div>
      </div>
      <div class="mt-2 space-y-2">
        <a href="/" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Home</a>
        <a href="/services" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Services</a>
        <a href="/profile" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Profile</a>
      </div>
    </div>
  </div> --}}
</header>
