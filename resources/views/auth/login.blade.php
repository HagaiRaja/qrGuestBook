<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Convite</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{env('APP_URL')}}/img/logo.png" />

    

    <!-- Bootstrap core CSS -->
<link href="{{env('APP_URL')}}/css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="{{env('APP_URL')}}/css/signin.css" rel="stylesheet">
  </head>
  <body class="text-center">
    
<main class="form-signin">
  <form method="POST" action="{{env('APP_URL')}}/login">
    @csrf
    <a href="/">
      <img class="mb-4" src="{{env('APP_URL')}}/img/logo.png" alt="" width="72" height="57">
    </a>
    <h1 class="h3 mb-3 fw-normal">Convite</h1>

    <div class="form-floating">
      <input id="email" class="form-control" type="email" name="email" :value="old('email')" required placeholder="name@example.com">
      <label for="floatingInput" for="email" :value="__('Email')">Email address</label>
    </div>
    <div class="form-floating">
      <input id="password" class="form-control"
      type="password"
      name="password"
      required autocomplete="current-password" placeholder="Password">
      <label for="password" :value="__('Password')">Password</label>
    </div>

    <div class="checkbox mb-3">
      <label for="remember_me" class="inline-flex items-center">
        <input type="checkbox" id="remember_me" value="remember-me" name="remember">
        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
      </label>
    </div>
    <button class="w-100 btn btn-lg btn-primary" type="submit">{{ __('Log in') }}</button>
    <p class="mt-5 mb-3 text-muted">&copy; 2022</p>
  </form>
</main>


    
  </body>
</html>
