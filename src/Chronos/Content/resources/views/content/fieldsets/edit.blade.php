@extends('chronos::content')

@section('content')
    <header class="subheader">
        <h1>{{ get_class($parent) == 'Chronos\Content\Models\ContentType' ? $parent->name : $parent->title }}</h1>
        <ul class="breadcrumbs">
            <li><span class="icon c4icon-pencil-3"></span></li>
            @if (get_class($parent) == 'Chronos\Content\Models\ContentType')
                <li><a href="{{ route('chronos.content.types') }}">{!! trans('chronos.content::interface.Content types') !!}</a></li>
                <li><a href="{{ route('chronos.content.types.edit', ['id' => $type->id]) }}">{{ $type->name }}</a></li>
            @else
                <li><a href="{{ route('chronos.content', ['type' => $type->id]) }}">{{ $type->name }}</a></li>
                <li class="active"><a href="{{ route('chronos.content.edit', ['type' => $type->id, 'id' => $parent->id]) }}">{{ $parent->title }}</a></li>
            @endif
            <li class="active">Fieldsets</li>
        </ul>
    </header><!--/.subheader -->
    <div class="container">
        <fieldset-editor v-bind:parent-id="{{ $parent->id }}" parent-type="{{ get_class($parent) == 'Chronos\Content\Models\ContentType' ? 'ContentType' : 'Content' }}" v-bind:type-id="{{ $type->id }}" inline-template>
            {!! Form::open(['route' => get_class($parent) == 'Chronos\Content\Models\ContentType' ? ['api.content.types.fieldset', $parent] : ['api.content.fieldset', $type, $parent], 'method' => 'PATCH', 'v-on:submit.prevent' => 'saveFieldsets', 'novalidate' => 'novalidate']) !!}
            <div class="row">
                <div class="col-xs-12 col-md-8">
                    <div id="fieldset-editor-wrapper">
                        <p class="text-center" v-show="dataLoader"><span class="loader-small"></span></p>

                        <div class="panel" v-if="!dataLoader && (!fieldsets || fieldsets.length == 0)">
                            <h2 class="panel-title">{!! trans('chronos.content::interface.Fieldsets') !!}</h2>
                            <p class="no-results">{!! trans('chronos.content::interface.This content type has no fieldsets. Create one now.') !!}</p>
                            <p class="text-center"><a class="btn btn-action add-fieldset" v-on:click="addFieldset">{!! trans('chronos.content::forms.Add fieldset') !!}</a></p>
                        </div>

                        <div class="fieldset-list" v-if="!dataLoader">
                            <set v-for="fieldset in fieldsets" v-bind:key="fieldset.key" v-bind:order="fieldset.order" v-bind:fieldset-data="fieldset"></set>
                        </div>

                        <a class="btn btn-action add-fieldset" v-if="!dataLoader && (fieldsets && fieldsets.length > 0)" v-on:click="addFieldset()">{!! trans('chronos.content::forms.Add fieldset') !!}</a>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4">
                    <div class="content-sidebar" data-spy="affix" data-offset-top="100">
                        <div class="panel panel-actions">
                            <button class="btn btn-primary" name="process" type="submit" value="1">{!! trans('chronos.content::forms.Save') !!}</button>
                            <a class="btn btn-cancel" href="{{ get_class($parent) == 'Chronos\Content\Models\ContentType' ? route('chronos.content.types') : route('chronos.content', ['type' => $type->id]) }}">{!! trans('chronos.content::forms.Cancel') !!}</a>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </fieldset-editor>
    </div>
@endsection



@push('scripts-components')
@include('chronos::components.fieldset_editor')
@endpush