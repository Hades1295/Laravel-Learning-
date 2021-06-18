@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Laravel 8 CRUD Example </h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ url('create') }}" title="Create a product"> <i class="fas fa-plus-circle"></i>
                    </a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    {{  $i=1 }}
    <table class="table table-bordered table-responsive-lg">
      
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Sku</th>
            <th>description</th>
            <th>Price</th>
            <th>Date Created</th>
            <th>Actions</th>
        </tr>
        @foreach ($products as $product)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$product->name}}</td>
                <td>{{$product->sku}}</td>
                <td>{{$product->description}}</td>
                <td>{{$product->price}}</td>
                <td>{{$product->created_at}}</td>
                <td> 
                 <!-- Edit -->
                 <a href="{{ route('products.edit',[$product->id]) }}" class="btn btn-sm btn-info">Edit</a>
                 <!-- Delete -->
                 <a href="{{ route('products.delete',[$product->id]) }}" class="btn btn-sm btn-danger">Delete</a>
                <!--Show-->
                <a href="{{ route('products.show',[$product->id]) }}" title="show">
                    <i class="fas fa-eye text-success  fa-lg"></i>
                </a>   
                </td> 
            </tr>
        @endforeach
    </table>

    {!! $products->links() !!}

@endsection
