@extends('jotform_api::base')


@section('content')
	<div class="container-fluid">

		<h1 class="page-title">
        	<i class="voyager-warning"></i>  Error
            <br>
            
        </h1>

    </div>

    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    
                    <div class="panel-body">
                        
                        <h4>{{ $content }}</h4>
                    </div>

                </div>
            </div>
        </div>

    </div>

@stop

@section('css')

@stop