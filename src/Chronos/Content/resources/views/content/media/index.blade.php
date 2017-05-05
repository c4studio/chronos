@extends('chronos::content')

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <header class="subheader">
                <h1>{!! trans('chronos.content::interface.Media') !!}</h1>
                <ul class="breadcrumbs">
                    <li><span class="icon c4icon-pencil-3"></span></li>
                    <li class="active">{!! trans('chronos.content::interface.Media') !!}</li>
                </ul>
                @can ('upload_media')
                <div class="main-action create">
                    <ajax-upload action="{{ route('api.content.media.store') }}" v-bind:multiple="true">
                        <a data-placement="left" data-tooltip="tooltip" title="{!! trans('chronos.content::interface.Upload media') !!}">{!! trans('chronos.content::interface.Upload media') !!}</a>
                    </ajax-upload>
                </div>
                @endcan
            </header><!--/.subheader -->

            <media-table></media-table>
        </div><!--/.content -->
    </div><!--/.content-wrapper -->
@endsection



@push('content-modals')
    <div class="modal fade" id="delete-file-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-danger">
                <form v-on:submit.prevent="deleteModelFromDialog">
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.content::interface.Delete file') !!}</h4>
                    </div>
                    <div class="modal-body">
                        <p class="marginT15 text-center"><strong>{!! trans('chronos.content::interface.WARNING! This action is irreversible.') !!}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.content::interface.Close') !!}</button>
                        <button class="btn btn-danger" name="process" type="submit" value="1">{!! trans('chronos.content::interface.Delete') !!}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush