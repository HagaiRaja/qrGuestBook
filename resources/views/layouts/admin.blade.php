@extends('layouts.master')

@section('navlinks')
@endsection

@section('sidebar')
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
         with font-awesome or any other icon font library -->
    <li class="nav-item has-treeview">
      <a href="{{env('APP_URL')}}/guests" class="nav-link {{ (request()->route()->getName()=='guest.index')?'active':''}}">
        <i class="nav-icon fas fa-book"></i>
        <p>
          Guest List
        </p>
      </a>
    </li>
    <li class="nav-item has-treeview">
      <a href="{{env('APP_URL')}}/scanners" class="nav-link {{ (request()->route()->getName()=='scanner.index')?'active':''}}">
        <i class="nav-icon fas fa-barcode"></i>
        <p>
          Scanner
        </p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{env('APP_URL')}}/scanners/show" class="nav-link">
      <i class="nav-icon far fa-circle text-success"></i>
      <p class="text">SCAN!</p>
      </a>
    </li>
  </ul>
</nav>
@endsection

@section('content')
@yield('content-wrapper')
@endsection

@section('js')
@yield('js')
@endsection