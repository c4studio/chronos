@extends('chronos::admin')

@section('content')
    <header class="subheader">
        <h1>{!! trans('chronos.content::interface.Languages') !!}</h1>
        <ul class="breadcrumbs">
            <li><span class="icon c4icon-pencil-3"></span></li>
            <li class="active">{!! trans('chronos.content::interface.Languages') !!}</li>
        </ul>
        @if (settings('is_multilanguage'))
        <div class="main-action create">
            <a data-placement="left" data-tooltip="tooltip" title="{!! trans('chronos.content::interface.Add language') !!}" data-toggle="modal" data-target="#add-language-dialog">{!! trans('chronos.content::interface.Add language') !!}</a>
        </div>
        @endif
    </header><!--/.subheader -->

    @if (settings('is_multilanguage'))
    <data-table inline-template default-sort-field="name" v-bind:sort-reverse="true" v-bind:with-inactive="true" src="{{ route('api.settings.languages') }}">
        <div id="data-table-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="filter-bar">
                            <div class="search">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="c4icon-2x c4icon-search-2"></span></span>
                                    <input class="form-control" type="text" placeholder="{!! trans('chronos.content::interface.Search') !!}" v-on:keyup="search" v-model="filters.search" />
                                    <span class="input-group-addon reset" v-on:click="clearSearch"><span class="c4icon-lg c4icon-cross-2"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel">
                            <table class="table table-condensed" v-cloak>
                                <thead>
                                <tr>
                                    <th><sortable field="name">{!! trans('chronos.content::interface.Name') !!}</sortable></th>
                                    <th><sortable field="status">{!! trans('chronos.content::interface.Status') !!}</sortable></th>
                                    <th>{!! trans('chronos.content::interface.Actions') !!}</th>
                                </tr>
                                </thead>
                                <tbody v-show="!dataLoader && data.length !== 0">
                                <tr v-for="item in data">
                                    <td><em v-html="highlight(item.name, filters.search)"></em></td>
                                    <td v-html="item.status ? '{!! trans('chronos.content::interface.Active') !!}' : '{!! trans('chronos.content::interface.Inactive') !!}'"></td>
                                    <td>
                                        <a class="marginR15" v-on:click="ajaxGet(item.endpoints.activate, true)" v-if="!item.status">{!! trans('chronos.content::interface.Activate') !!}</a>
                                        <a class="marginR15" v-on:click="ajaxGet(item.endpoints.deactivate, true)" v-if="item.status">{!! trans('chronos.content::interface.Deactivate') !!}</a>
                                        <a data-toggle="modal" data-target="#delete-language-dialog" v-on:click="setdeleteURL(item.endpoints.destroy)">{!! trans('chronos.content::interface.Delete') !!}</a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <p class="text-center" v-show="dataLoader"><span class="loader-small"></span></p>
                            <p class="no-results" v-show="!dataLoader && data.length === 0">{!! trans('chronos.content::interface.There are no results here. Try broadening your search.') !!}</p>
                        </div>
                    </div>
                </div>
            </div>

            @include('chronos::components.pagination')
        </div>
    </data-table>
    @else
        <p class="no-results">{!! trans('chronos.content::interface.Multilanguage support is deactivated.') !!}</p>
    @endif
@endsection

@push('content-modals')
    <div class="modal fade" id="add-language-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <language-editor inline-template>
                    {!! Form::open(['route' => 'api.settings.languages.store', 'method' => 'POST', 'v-on:submit.prevent' => 'saveForm', 'novalidate' => 'novalidate']) !!}
                        <div class="modal-header">
                            <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                            <h4 class="modal-title">{!! trans('chronos.content::interface.Add language') !!}</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'code') }">
                                <label class="control-label label-req" for="code">{!! trans('chronos.content::forms.Select language') !!}</label>
                                <autocomplete input-class="form-control" id="code" name="code" src="{{ route('api.settings.languages.all') }}" label-field="name" value-field="code"></autocomplete>
                                <span class="help-block" v-html="store.formErrors['code'][0]" v-if="Object.hasKey(store.formErrors, 'code')"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.content::interface.Close') !!}</button>
                            <button class="btn btn-primary" name="process" type="submit" value="1">{!! trans('chronos.content::forms.Save') !!}</button>
                        </div>
                    {!! Form::close() !!}
                </language-editor>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete-language-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-danger">
                <form v-on:submit.prevent="deleteModelFromDialog">
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.content::interface.Delete language') !!}</h4>
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



@push('scripts-components')
    @include('chronos::components.language_editor')
@endpush