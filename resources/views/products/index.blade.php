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

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">All Product</h3>
              </div>

                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Product Name</label>
                    <div class="col-sm-5">
                      <select class="form-control" id="products" name="products">
                          <option value="">Select..</option>
                          @foreach($qb_products as $product)
                          <option value="{{$product->Id}}">{{$product->FullyQualifiedName}}</option>
                          @endforeach
                      </select>
                    </div>
                    <a href="/settings/products/create/" id="createUrl" class="btn btn-info">Associate new product</a>
                  </div>


                </div>

              <div class="card-body">
              <!-- /.card-header -->
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($products as $product)

                  <tr>
                    <td width="50%">{{$product->name}}</td>
                    <td width="30%">{{$product->type}}</td>
                    <!--<td><a href="#" class="btn btn-success">Edit</a> | <a href="#" class="btn btn-danger">Delete</a></td>-->
                    <td><a href="{{route('products.destroy',['id'=> $product->id])}}" class="btn btn-danger">Delete</a></td>
                  </tr>

                  @endforeach
                  </tbody>
                  <tfoot>

                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
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
    <script
        src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
        crossorigin="anonymous"></script>
    <script
        src="https://code.jquery.com/jquery-migrate-3.3.2.min.js"
        integrity="sha256-Ap4KLoCf1rXb52q+i3p0k2vjBsmownyBTE1EqlRiMwA="
        crossorigin="anonymous"></script>
    <script>

        $( document ).ready(function() {
            $( "#products" ).change(function() {
                var id = $("#products").val();
                console.log(id);
                $('#createUrl').attr("href", "/settings/products/create/" + id);
            });
        });


    </script>
@endsection
