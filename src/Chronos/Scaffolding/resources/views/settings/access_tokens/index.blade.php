@extends('chronos::admin')

@section('content')

    <div class="content-wrapper">
        <div class="content">
            <header class="subheader">
                <h1>{!! trans('chronos.scaffolding::interface.Access tokens') !!}</h1>
                <ul class="breadcrumbs">
                    <li><span class="icon c4icon-pencil-3"></span></li>
                    <li class="active">{!! trans('chronos.scaffolding::interface.Access tokens') !!}</li>
                </ul>
                <div class="main-action create">
                    <a data-placement="left" data-tooltip="tooltip" title="{!! trans('chronos.scaffolding::interface.Create access token') !!}" data-toggle="modal" data-target="#create-access-token-dialog">{!! trans('chronos.scaffolding::interface.Create access token') !!}</a>
                </div>
            </header><!--/.subheader -->

            <data-table inline-template default-sort-field="name" v-bind:sort-reverse="true" src="{{ route('api.settings.access_tokens') }}">
                <div id="data-table-wrapper">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="filter-bar">
                                    <div class="search">
                                        <div class="input-group">
                                            <span class="input-group-addon"><span class="c4icon-2x c4icon-search-2"></span></span>
                                            <input class="form-control" type="text" placeholder="{!! trans('chronos.scaffolding::interface.Search') !!}" v-on:keyup="search" v-model="filters.search" />
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
                                            <th><sortable field="name">{!! trans('chronos.scaffolding::interface.Name') !!}</sortable></th>
                                            <th>{!! trans('chronos.scaffolding::interface.Actions') !!}</th>
                                        </tr>
                                        </thead>
                                        <tbody v-show="!dataLoader && data.length !== 0">
                                        <tr v-for="item in data">
                                            <td><em v-html="highlight(item.name, filters.search)"></em></td>
                                            <td><a data-toggle="modal" data-target="#view-access-token-modal" v-on:click="setInnerHTML('#view-access-token-modal .modal-title', item.name); setInnerHTML('#view-access-token-modal .token', item.access_token); " class="marginR15">{!! trans('chronos.scaffolding::interface.View') !!}</a> @can('delete_access_tokens')<a data-toggle="modal" data-target="#delete-access-token-dialog" v-on:click="setdeleteURL(item.endpoints.destroy)">{!! trans('chronos.scaffolding::interface.Delete') !!}</a>@endcan</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <p class="text-center" v-show="dataLoader"><span class="loader-small"></span></p>
                                    <p class="no-results" v-show="!dataLoader && data.length === 0">{!! trans('chronos.scaffolding::interface.There are no results here. Try broadening your search.') !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('chronos::components.pagination')
                </div>
            </data-table>
        </div><!--/.content -->
    </div><!--/.content-wrapper -->
@endsection

@push('content-modals')
    <div class="modal fade" id="create-access-token-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <access-token-editor inline-template>
                    {!! Form::open(['route' => 'api.settings.access_tokens.store', 'method' => 'POST', 'v-on:submit.prevent' => 'saveForm', 'novalidate' => 'novalidate']) !!}
                        <div class="modal-header">
                            <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                            <h4 class="modal-title">{!! trans('chronos.scaffolding::interface.Create access token') !!}</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'name') }">
                                <label class="control-label label-req" for="name">{!! trans('chronos.scaffolding::forms.Name') !!}</label>
                                <input class="form-control" id="name" name="name" type="text" v-model="name" />
                                <span class="help-block" v-html="store.formErrors['name'][0]" v-if="Object.hasKey(store.formErrors, 'name')"></span>
                                <span class="help-block" v-else><span>{!! trans('chronos.scaffolding::forms.The name of the access token must be unique.') !!}</span></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.scaffolding::interface.Close') !!}</button>
                            <button class="btn btn-primary" name="process" type="submit" value="1">{!! trans('chronos.scaffolding::forms.Save') !!}</button>
                        </div>
                    {!! Form::close() !!}
                </access-token-editor>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete-access-token-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-danger">
                <form v-on:submit.prevent="deleteModelFromDialog">
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.scaffolding::interface.Delete access token') !!}</h4>
                    </div>
                    <div class="modal-body">
                        <p class="marginT15 text-center"><strong>{!! trans('chronos.scaffolding::interface.WARNING! This action is irreversible.') !!}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.scaffolding::interface.Close') !!}</button>
                        <button class="btn btn-danger" name="process" type="submit" value="1">{!! trans('chronos.scaffolding::interface.Delete') !!}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="view-access-token-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-danger">
                <div class="modal-header">
                    <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p class="marginT15"><strong>{!! trans('chronos.scaffolding::interface.Access token') !!}</strong></p>

                    <pre><code class="token"></code></pre>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.scaffolding::interface.Close') !!}</button>
                </div>
            </div>
        </div>
    </div>
@endpush



@push('scripts-components')
    @include('chronos::components.access_token_editor')
@endpush