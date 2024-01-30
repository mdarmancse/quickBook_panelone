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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Quickbooks Settings Update</h3>
                    </div>

                    <form class="form-horizontal" action="{{ route('settings.store', ['settings'=> $settings ])}}" method="POST">
                        @csrf
                        @method('POST')
                        @include('flash-message')

                        <div class="card-body">
                            <div class="form-group row">
                                <label for="ClientID" class="col-sm-3 col-form-label">Client ID</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="ClientID" name="ClientID" value="{{ $settings->ClientID }}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ClientSecret" class="col-sm-3 col-form-label">Client Secret</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="ClientSecret" name="ClientSecret" value="{{ $settings->ClientSecret }}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="RedirectURI" class="col-sm-3 col-form-label">Redirect URL</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="RedirectURI" name='RedirectURI'  value="{{ route('qb.callback', ['user' => auth()->user()->id]) }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="scope" class="col-sm-3 col-form-label">Scope</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="scope" name='scope'  value="{{ $settings->scope }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="baseUrl" class="col-sm-3 col-form-label">Base URL</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="baseUrl" name='baseUrl'  value="{{ $settings->baseUrl }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="QBORealmID" class="col-sm-3 col-form-label">QBO Realm ID</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="QBORealmID" name='QBORealmID'  value="{{ $settings->QBORealmID }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="others" class="col-sm-3 col-form-label">Others (Optional)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="others" name="others"  value="{{ $settings->others }}">
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-info">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- /.content -->

@endsection
