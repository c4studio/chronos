@extends('chronos::content')

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <header class="subheader">
                <h1>{{ $type->name }}</h1>
                <ul class="breadcrumbs">
                    <li><span class="icon c4icon-pencil-3"></span></li>
                    <li class="active">{{ $type->name }}</li>
                </ul>
                @can ('add_content_type_' . $type->id)
                <div class="main-action create">
                    <a href="{{ route('chronos.content.create', ['type' => $type->id]) }}" data-placement="left" data-tooltip="tooltip" title="{!! trans('chronos.content::interface.Create new :type', ['type' => strtolower($type->name)]) !!}">{!! trans('chronos.content::interface.Create new :type', ['type' => strtolower($type->name)]) !!}</a>
                </div>
                @endcan
            </header><!--/.subheader -->

            <data-table inline-template default-sort-field="title" v-bind:sort-reverse="true" v-bind:with-inactive="true" src="{{ route('api.content', [$type->id]) }}">

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
                                            <th><sortable field="title">{!! trans('chronos.content::interface.Title') !!}</sortable></th>
                                            @if (settings('is_multilanguage') && $type->translatable)
                                                @foreach($languages as $language)
                                                    <th>{{ strtoupper($language->code) }}</th>
                                                @endforeach
                                            @endif
                                            <th><sortable field="status">{!! trans('chronos.content::interface.Status') !!}</sortable></th>
                                            <th>{!! trans('chronos.content::interface.Actions') !!}</th>
                                        </tr>
                                        </thead>
                                        <tbody v-show="!dataLoader && data.length !== 0">
                                        <tr v-for="item in data">
                                            <td><em v-html="highlight(item.title, filters.search)"></em></td>
                                            @if (settings('is_multilanguage') && $type->translatable)
                                                @foreach($languages as $language)
                                                    <td>
                                                        <a class="icon c4icon-check-2" v-bind:href="item.admin_urls.edit" v-if="item.language == '{{ $language->code }}'"></a>
                                                        <a class="icon c4icon-pencil-3" v-bind:href="item.admin_urls.translations.{{ $language->code }}" v-if="item.language != '{{ $language->code }}' && item.translation_codes.indexOf('{{ $language->code }}') !== -1"></a>
                                                        <a class="icon c4icon-plus-2" v-bind:href="item.endpoints.translate + '?language={{ $language->code }}'" v-if="item.language != '{{ $language->code }}' && item.translation_codes.indexOf('{{ $language->code }}') === -1"></a>
                                                    </td>
                                                @endforeach
                                            @endif
                                            <td v-html="item.status ? '{!! trans('chronos.content::interface.Active') !!}' : '{!! trans('chronos.content::interface.Inactive') !!}'"></td>
                                            <td>
                                                @can ('edit_content_type_' . $type->id)<a v-bind:href="item.admin_urls.edit" class="marginR15">{!! trans('chronos.content::interface.Edit') !!}</a>@endcan
                                                @can ('edit_content_type_fieldsets_' . $type->id)<a v-bind:href="item.admin_urls.edit_fieldsets" class="marginR15">{!! trans('chronos.content::interface.Edit fieldsets') !!}</a>@endcan
                                                @can ('delete_content_type_' . $type->id)<a data-toggle="modal" data-target="#delete-content-dialog" v-on:click="setdeleteURL(item.endpoints.destroy, $event)" v-if="!item.lock_delete">{!! trans('chronos.content::interface.Delete') !!}</a>@endcan
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
        </div><!--/.content -->
    </div><!--/.content-wrapper -->
@endsection



@push('content-modals')
<div class="modal fade" id="delete-content-dialog" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-danger">
            <form v-on:submit.prevent="deleteModelFromDialog">
                <div class="modal-header">
                    <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                    <h4 class="modal-title">{!! trans('chronos.content::interface.Delete content') !!}</h4>
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