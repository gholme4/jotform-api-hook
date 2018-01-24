@extends('jotform_api::base')

@section('css')

@endsection


@section('content')
	<div class="container-fluid">

		<h1 class="page-title">
        	<i class="voyager-file-text"></i> {{ $form->title }} <strong>Form Submissions</strong> 
            <br>
            
        </h1>

    </div>

    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        @if (count($submissions) > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    
                    <div class="panel-body">
                        
                        

                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                      
                                        <th>ID</th>
                                        <th>Updated At</th>
                                     
                                        <th>{{ __('voyager.generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $submission)
                                    <tr>
                                       
                                        <td>{{ $submission->id }}</td>
                                        <td>{{ $submission->updated_at }}</td>

                                        <td>
                                            <a href="{{ route('voyager.jotform_api.submission', [ 'submissionId' => $submission->id ]) }}" title="View" class="btn btn-sm btn-warning pull-right">
                                                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">View Submission</span>
                                            </a>
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

        <a href="{{ route('voyager.jotform_api.export_form_submissions', [ 'formId' => $form->id ]) }}" title="Excel download" class="btn btn-sm btn-warning ">
            <i class="voyager-download"></i> <span>Export as Excel file</span>
        </a>
        @else

            <h2> No submissions</h2>
        @endif
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
