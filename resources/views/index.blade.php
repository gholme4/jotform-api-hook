@extends('jotform_api::base')

@section('css')

@endsection


@section('content')
	<div class="container-fluid">

		<h1 class="page-title">
        	<i class="voyager-file-text"></i> Jotform Forms
        	<small>View all forms</small>
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
                                      
                                        <th>Name</th>
                                        <th>Updated At</th>
                                        <th># Submissions</th>
                                           
                                        <th>{{ __('voyager.generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($forms as $form)
                                    <tr>
                                        <td>{{ $form->title }}</td>
                                        <td>{{ $form->updated_at }}</td>
                                        <td class="text-right">{{ $form->count }}</td>
                                        <td>
                                            @if ($form->count > 0)
                                          	<a href="{{ route('voyager.jotform_api.form', [ 'formId' => $form->id ]) }}" title="View" class="btn btn-sm btn-warning pull-right">
                                                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">View Submissions</span>
                                            </a>
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
                    "language" => __('voyager.datatable'),
                ],
                config('voyager.dashboard.data_tables', []))
            , true) !!});
            
            
        });


    </script>
@stop
