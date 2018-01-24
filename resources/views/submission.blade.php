@extends('jotform_api::base')

@section('css')

@endsection


@section('content')
	<div class="container-fluid">

		<h1 class="page-title">
        	<i class="voyager-file-text"></i> <strong>Submission from</strong> {{ $form->title }}
            <small>Updated at {{ $submission->updated_at }}</small>
            <br>
            
        </h1>

    </div>

    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    
                    <div class="panel-body">
                        
                        

                        <div class="table-responsive">
                           <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                      
                                        <th>Field</th>
                                        <th>Answer</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submission->answers as $answer)
                                    <tr>
                                       
                                        <td>{{ $answer->text }}</td>
                                        
                                        <td>
                                        @if ( property_exists($answer, 'answer') ) 
                                            @if ( is_array($answer->answer) || is_object($answer->answer) ) 
                                                {{ json_encode($answer->answer) }} 
                                            @else
                                                {{ $answer->answer }} 
                                            @endif
                                        @endif
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="pull-left">
                            <div role="status" class="show-res" aria-live="polite"></div>
                        </div>
                        <div class="pull-right">
                        	
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

@stop

@section('css')
@if( config('dashboard.data_tables.responsive'))
<link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@endif
@stop

@section('javascript')
    <!-- DataTables -->
    @if(config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    <script>
        $(document).ready(function () {
            var table = $('#dataTable').DataTable({!! json_encode(
                array_merge([
                    "order" => [],
                    "paging" => false,
                    "language" => __('voyager.datatable'),
                ],
                config('voyager.dashboard.data_tables', []))
            , true) !!});
            
            
        });


    </script>
@stop
