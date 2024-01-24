@extends('layouts.master')

@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Horizontal Form -->
                        <div class="card card-info">
                            <div class="card-header" style="margin-bottom:5px">
                                <h3 class="card-title">Create Product</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form style="margin: 10px" class="form-horizontal" action="{{route('products.store')}}" enctype="multipart/form-data" method="POST">
                                @csrf
                                @include('qb-flash-message')


                                <div class="clone">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Item Name</label>
                                                <input type="text" value="{{$product->FullyQualifiedName}}" class="form-control" name="name" id="name" placeholder="Enter Item Name" required="">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea type="text" class="form-control" id="description" name="description" >{{$product->Description}}</textarea>

                                            </div>
                                        </div>


                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label>Posting Type</label>
                                                <select class="form-control" required="" id="type" name="type">
                                                    <option value="Debit">Debit</option>
                                                    <option value="Credit">Credit</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Chart of Account</label>
                                                <select class="form-control" required="" id="coa" name="coa">
                                                    @foreach($coas as $coa)
                                                    <option value="{{$coa->Id}}">{{$coa->FullyQualifiedName}} - {{$coa->Classification}}- {{$coa->AccountType}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" value="{{$product->Id}}" id="product_qb_id" name="product_qb_id">
                                    </div>
                                </div>

                                <!-- /.card-body -->
                                <div class="card-footer text-center">
                                    <button type="submit" class="btn btn-info">Submit</button>

                                </div>
                                <!-- /.card-footer -->
                            </form>
                        </div>
                        <!-- /.card -->

                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection
