@extends('chronos::admin')

@push('styles')
<link href="{{ asset('chronos/css/content.css?v=' . time()) }}" rel="stylesheet" /> <!-- @TODO remove cache-busting -->
@endpush

@push('content-modals')
    <div class="modal fade" id="media-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full-width" role="document">
            <div class="modal-content modal-body-bg">

                <media-table v-bind:selectable="true" v-bind:autoload="false"></media-table>

            </div>
        </div>
    </div>
@endpush

@push('scripts-components')
    @include('chronos::components.media_file')
    @include('chronos::components.media_table')
    @include('chronos::components.wysiwyg')
@endpush