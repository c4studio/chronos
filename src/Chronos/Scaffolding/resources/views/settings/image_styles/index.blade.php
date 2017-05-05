@extends('chronos::admin')

@section('content')

    <div class="content-wrapper">
        <div class="content">
            <header class="subheader">
                <h1>{!! trans('chronos.scaffolding::interface.Image styles') !!}</h1>
                <ul class="breadcrumbs">
                    <li><span class="icon c4icon-pencil-3"></span></li>
                    <li class="active">{!! trans('chronos.scaffolding::interface.Image styles') !!}</li>
                </ul>
                <div class="main-action dropdown">
                    <a data-placement="left" data-tooltip="tooltip" title="{!! trans('chronos.scaffolding::interface.Select action') !!}" data-toggle="dropdown">{!! trans('chronos.scaffolding::interface.Create image style') !!}</a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="{{ route('chronos.settings.image_styles.create') }}">{!! trans('chronos.scaffolding::interface.Create image style') !!}</a></li>
                        <li><a v-on:click="ajaxGet('{{ route('api.settings.image_styles.regenerate') }}')">{!! trans('chronos.scaffolding::interface.Regenerate image styles') !!}</a></li>
                    </ul>
                </div>
            </header><!--/.subheader -->

            <data-table inline-template default-sort-field="name" v-bind:sort-reverse="true" src="{{ route('api.settings.image_styles') }}">
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
                                            <td>
                                                @can('edit_image_styles')<a class="marginR15" v-bind:href="item.admin_urls.edit">{!! trans('chronos.scaffolding::interface.Edit') !!}</a>@endcan
                                                @can('delete_image_styles')<a class="marginR15" data-toggle="modal" data-target="#delete-image-style-dialog" v-on:click="setdeleteURL(item.endpoints.destroy)">{!! trans('chronos.scaffolding::interface.Delete') !!}</a>@endcan
                                                <a v-on:click="ajaxGet(item.endpoints.regenerate)">{!! trans('chronos.scaffolding::interface.Regenerate styles') !!}</a>
                                            </td>
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
    <div class="modal fade" id="delete-image-style-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-danger">
                <form v-on:submit.prevent="deleteModelFromDialog">
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.scaffolding::interface.Delete image style') !!}</h4>
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
@endpush
