<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
	<style>html{visibility: hidden;opacity:0;}</style>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fimnet Client Portal</title>
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
  	<link rel="apple-touch-icon" sizes="180x180" href="{{asset('favicon/apple-touch-icon.png')}}">
  	<link rel="icon" type="image/png" sizes="32x32" href="{{asset('favicon/favicon-32x32.png')}}">
  	<link rel="icon" type="image/png" sizes="16x16" href="{{asset('favicon/favicon-16x16.png')}}">
  	<link rel="manifest" href="{{asset('favicon/site.webmanifest')}}">
  	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  	<style>
  		html {
		    visibility: visible;
		    opacity: 1;
		}
  	</style>
</head>
<body class="h-full">
	 @yield('content')
</body>
