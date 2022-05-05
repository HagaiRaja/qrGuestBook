@extends('layouts.master')

@section('navlinks')
<li class="nav-item {{ (request()->route()->getName()=='dashboard.show')?'active':''}} d-vskjnone d-sm-inline-block">
  <a href="{{ url('/') }}" class="nav-link">1. DATA SISWA</a>
</li>
<li class="nav-item {{ (request()->route()->getName()=='parent.show')?'active':''}} d-none d-sm-inline-block">
  <a href="{{ url('/parent') }}" class="nav-link">2. DATA ORANG TUA</a>
</li>
<li class="nav-item {{ (request()->route()->getName()=='guardian.show')?'active':''}} d-none d-sm-inline-block">
  <a href="{{ url('/guardian') }}" class="nav-link">3. DATA WALI</a>
</li>
<li class="nav-item {{ (request()->route()->getName()=='document.show')?'active':''}} d-none d-sm-inline-block">
  <a href="{{ url('/document') }}" class="nav-link">4. UPLOAD BERKAS</a>
</li>
<li class="nav-item {{ (request()->route()->getName()=='test.show')?'active':''}} d-none d-sm-inline-block">
  <a href="{{ url('/test') }}" class="nav-link">5. TES</a>
</li>
<li class="nav-item {{ (request()->route()->getName()=='bill.show')?'active':''}} d-none d-sm-inline-block">
  <a href="{{ url('/bill') }}" class="nav-link">6. PEMBAYARAN</a>
</li>
@if (auth()->user()->student->status == 'Lulus')
<li class="nav-item {{ (request()->route()->getName()=='student.show')?'active':''}} d-none d-sm-inline-block">
  <a href="{{ url('/student') }}" class="nav-link">7. KELULUSAN</a>
</li>
@endif
@endsection

@section('sidebar')
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
         with font-awesome or any other icon font library -->
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link active">
        <i class="nav-icon fas fa-edit"></i>
        <p>
          Form Pendaftaran
        </p>
      </a>
    </li>
  </ul>
</nav>
@endsection

@section('content')
@yield('content-wrapper')
<live-toaster 
    status="{{ auth()->user()->student->status }}" 
    is-filled="{{ auth()->user()->is_filled() }}" 
    is-paid="{{ auth()->user()->is_paid() }}"/>
@endsection

@section('js')
@yield('js')
@endsection