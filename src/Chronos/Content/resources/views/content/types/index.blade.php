@extends('chronos::content')

@section('content')
    <header class="subheader">
        <h1>{!! trans('chronos.content::interface.Content types') !!}</h1>
        <ul class="breadcrumbs">
            <li><span class="icon c4icon-pencil-3"></span></li>
            <li class="active">{!! trans('chronos.content::interface.Content types') !!}</li>
        </ul>
        @can ('add_content_types')
        <div class="main-action create">
            <a data-placement="left" data-tooltip="tooltip" title="{!! trans('chronos.content::interface.Create content type') !!}" data-toggle="modal" data-target="#create-content-type-dialog">{!! trans('chronos.content::interface.Create content type') !!}</a>
        </div>
        @endcan
    </header><!--/.subheader -->

    <data-table inline-template default-sort-field="name" v-bind:sort-reverse="true" src="{{ route('api.content.types') }}">
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
                                        @if (Gate::check('delete_content_types') || Gate::check('export_content_types'))
                                        <th>
                                            <div class="bulk-actions">
                                                <input type="checkbox" v-on:change="selectAll" v-model="bulkSelector" />
                                                <a class="bulk-actions-toggle" data-toggle="dropdown"></a>
                                                <ul class="dropdown-menu">
                                                    @can ('delete_content_types')
                                                    <li><a data-toggle="modal" data-target="#delete-content-types-dialog">{!! trans('chronos.content::interface.Delete') !!}</a></li>
                                                    @endcan
                                                    @can ('export_content_types')
                                                    <li><a v-on:click="performBulkAction('{{ route('api.content.types.export') }}', 'DOWNLOAD', 'types', $event)">{!! trans('chronos.content::interface.Export') !!}</a></li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </th>
                                        @endif
                                        <th><sortable field="name">{!! trans('chronos.content::interface.Name') !!}</sortable></th>
                                        <th><sortable field="items_count">{!! trans('chronos.content::interface.Items') !!}</sortable></th>
                                        <th>{!! trans('chronos.content::interface.Actions') !!}</th>
                                    </tr>
                                </thead>
                                <tbody v-show="!dataLoader && data.length !== 0">
                                    <tr v-for="item in data">
                                        @if (Gate::check('delete_content_types') || Gate::check('export_content_types'))
                                        <td><input v-bind:value="item.id" type="checkbox" v-model="selected" /></td>
                                        @endif
                                        <td><em v-html="highlight(item.name, filters.search)"></em><small v-if="item.notes != ''">@{{ item.notes|strLimit(50) }}</small></td>
                                        <td>@{{ item.items_count }}</td>
                                        <td>
                                            @can ('edit_content_types')<a v-bind:href="item.admin_urls.edit" class="marginR15">{!! trans('chronos.content::interface.Edit') !!}</a>@endcan
                                            @can ('edit_content_type_fieldsets')<a v-bind:href="item.admin_urls.edit_fieldsets" class="marginR15">{!! trans('chronos.content::interface.Edit fieldsets') !!}</a>@endcan
                                            @can ('delete_content_types')<a data-toggle="modal" data-target="#delete-content-type-dialog" v-on:click="setdeleteURL(item.endpoints.destroy, $event)">{!! trans('chronos.content::interface.Delete') !!}</a>@endcan
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
@endsection



@push('content-modals')
    <div class="modal fade" id="create-content-type-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <content-type-editor inline-template>
                    {!! Form::open(['route' => 'api.content.types.store', 'method' => 'POST', 'v-on:submit.prevent' => 'saveForm', 'novalidate' => 'novalidate']) !!}
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.content::interface.Create content type') !!}</h4>
                    </div>
                    <div class="modal-body">
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
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input id="translatable" name="translatable" type="checkbox" v-model="translatable" />
                                    {!! trans('chronos.content::forms.Translatable?') !!}
                                </label>
                            </div>

                            <span class="help-block">{!! trans('chronos.content::forms.Specifies whether this content type will be available in multiple languages.') !!}</span>
                        </div>
                        <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'notes') }">
                            <label class="control-label" for="notes">{!! trans('chronos.content::forms.Notes') !!}</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" v-model="notes"></textarea>
                            <span class="help-block" v-html="store.formErrors['notes'][0]" v-if="Object.hasKey(store.formErrors, 'notes')"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.content::interface.Close') !!}</button>
                        <button class="btn btn-primary" name="process" type="submit" value="1">{!! trans('chronos.content::forms.Save') !!}</button>
                    </div>
                    {!! Form::close() !!}
                </content-type-editor>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete-content-type-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-danger">
                <form v-on:submit.prevent="deleteModelFromDialog">
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.content::interface.Delete content type') !!}</h4>
                    </div>
                    <div class="modal-body">
                        <p class="marginT15 text-center"><strong>{!! trans('chronos.content::interface.WARNING! All content of this type will be deleted as well. This action is irreversible.') !!}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.content::interface.Close') !!}</button>
                        <button class="btn btn-danger" name="process" type="submit" value="1">{!! trans('chronos.content::interface.Delete') !!}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete-content-types-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-danger">
                <form v-on:submit.prevent="performBulkAction('{{ route('api.content.types.destroy_bulk') }}', 'DELETE', 'types', $event)">
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.content::interface.Delete content types') !!}</h4>
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
    @include('chronos::components.content_type_editor')
@endpush