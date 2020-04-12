@extends('layouts.app')

@section('content')
<div class="container">
    <div class="bladeHome row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Document Panel
                    <input type="button" class="btn btn-primary btn-md float-right" id="btnUpload" value="Upload Document">
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in {{ $user_name }}! <br/><br/>

                    <table id="dtForms" class="hover table" style="width:100%"></table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
