@extends('layouts.admin')
@section('title', 'Sunting Diskon')
@section('content')
    <h1>Sunting Diskon</h1>
    <div class="card" style="padding: 2rem;">
        <form action="{{ route('admin.discounts.update', $discount) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.discounts._form')
        </form>
    </div>
@endsection