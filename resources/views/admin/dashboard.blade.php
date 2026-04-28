@extends('layouts.admin')

@section('content')
    @include('admin.products', ['allProducts' => $allProducts])
    @include('admin.inventory')
    @include('admin.orders')
    @include('admin.users')
    @include('admin.support')
    @include('admin.settings')
@endsection
