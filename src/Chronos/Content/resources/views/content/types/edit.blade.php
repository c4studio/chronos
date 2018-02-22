@extends('chronos::content')

@section('content')
    <header class="subheader">
        <h1>{{ $type->name }}</h1>
        <ul class="breadcrumbs">
            <li><span class="icon c4icon-pencil-3"></span></li>
            <li><a href="{{ route('chronos.content.types') }}">{!! trans('chronos.content::interface.Content types') !!}</a></li>
            <li class="active">{{ $type->name }}</li>
        </ul>
    </header><!--/.subheader -->
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <h2 class="panel-title">{!! trans('chronos.content::interface.Content type info') !!}</h2>
                    <content-type-editor v-bind:type-id="{{ $type->id }}" inline-template>
                    {!! Form::model($type, ['route' => ['api.content.types.update', $type], 'method' => 'PATCH', 'v-on:submit.prevent' => 'saveForm', 'novalidate' => 'novalidate']) !!}
                        <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'name') }">
                            <label class="control-label label-req" for="name">{!! trans('chronos.content::forms.Name') !!}</label>
                            <input class="form-control" id="name" name="name" type="text" v-model="name" />
                            <span class="help-block" v-html="store.formErrors['name'][0]" v-if="Object.hasKey(store.formErrors, 'name')"></span>
                            <span class="help-block" v-else>{!! trans('chronos.content::forms.The human-readable name of this content type. This text will be displayed as part of the list on the <em>Create content</em> page. Must be unique.') !!}</span>
                        </div>
                        <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'title_label') }">
                            <label class="control-label label-req" for="title_label">{!! trans('chronos.content::forms.Title field label') !!}</label>
                            <input class="form-control" id="title_label" name="title_label" type="text" v-model="titleLabel" />
                            <span class="help-block" v-html="store.formErrors['title_label'][0]" v-if="Object.hasKey(store.formErrors, 'title_label')"></span>
                        </div>
                        @if (settings('is_multilanguage'))
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input id="translatable" name="translatable" type="checkbox" v-model="translatable" />
                                    {!! trans('chronos.content::forms.Translatable?') !!}
                                </label>
                            </div>

                            <span class="help-block">{!! trans('chronos.content::forms.Specifies whether this content type will be available in multiple languages.') !!}</span>
                        </div>
                        @endif
                        <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'notes') }">
                            <label class="control-label" for="notes">{!! trans('chronos.content::forms.Notes') !!}</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" v-model="notes"></textarea>
                            <span class="help-block" v-html="store.formErrors['notes'][0]" v-if="Object.hasKey(store.formErrors, 'notes')"></span>
                        </div>

                        <div class="panel-footer">
                            <a class="btn btn-cancel" href="{{ route('chronos.content.types') }}">{!! trans('chronos.content::forms.Cancel') !!}</a>
                            <button class="btn btn-primary" name="process" type="submit" value="1">{!! trans('chronos.content::forms.Save') !!}</button>
                        </div>
                    {!! Form::close() !!}
                    </content-type-editor>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('scripts-components')
    @include('chronos::components.content_type_editor')
@endpush