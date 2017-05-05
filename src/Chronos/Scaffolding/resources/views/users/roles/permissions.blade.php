@extends('chronos::admin')

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <header class="subheader">
                <h1>{!! trans('chronos.scaffolding::interface.Permissions') !!}</h1>
                <ul class="breadcrumbs">
                    <li><span class="icon c4icon-pencil-3"></span></li>
                    <li class="active">{!! trans('chronos.scaffolding::interface.Permissions') !!}</li>
                </ul>
            </header><!--/.subheader -->
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel">
                            <h2 class="panel-title">{!! trans('chronos.scaffolding::interface.Permissions') !!}</h2>
                            @if (count($roles) > 0)
                                <permissions-editor inline-template>
                                    {!! Form::open(['route' => 'api.users.permissions.update', 'method' => 'PATCH', 'v-on:submit.prevent' => 'saveForm', 'novalidate' => 'novalidate']) !!}
                                    <table class="table permissions">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                @if (count($roles) > 0)
                                                    @foreach ($roles as $role)
                                                        <th><span class="rotate">{{ ucfirst($role->name) }}</span></th>
                                                    @endforeach
                                                @endif
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if (count($permissions) > 0)
                                            @foreach ($permissions as $key => $group)
                                                <tr class="group"><td colspan="{{ count($roles) + 2 }}">{{ $key }}</td></tr>
                                                @if (count($group) > 0)
                                                    @foreach ($group as $k => $permission)
                                                        <tr>
                                                            <td width="200">{{ $k }}</td>
                                                            @if (count($roles) > 0)
                                                                @foreach ($roles as $role)
                                                                    <td width="50">{!! Form::checkbox('permission[' . $role->id . '][]', $permission['id'], $role->hasPermission($permission['name']) ? true : false, ['class' => 'select'] ) !!}</td>
                                                                @endforeach
                                                            @endif
                                                            <td></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>

                                    <div class="panel-footer">
                                        <button class="btn btn-primary" name="process" type="submit" value="1">{!! trans('chronos.scaffolding::forms.Save') !!}</button>
                                    </div>
                                    {!! Form::close() !!}
                                </permissions-editor>
                            @else
                                <p class="no-results">{!! trans('chronos.scaffolding::interface.There are no roles yet. Please <a href=":link">add a role</a> first.', ['link' => route('chronos.users.roles')]) !!}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div><!--/.content -->
    </div><!--/.content-wrapper -->
@endsection



@push('scripts-components')
    <script>
        Vue.component('permissions-editor', {
            data: function() {
                return {
                    store: vueStore.state
                }
            },
            methods: {
                saveForm: function(event) {
                    vm.$emit('show-loader');

                    var form = event.target;

                    var action = form.getAttribute('action');
                    var data = new FormData(form);
                    var method = form.getAttribute('method').toUpperCase();

                    this.$http({
                        body: data,
                        method: method,
                        url: action
                    }).then(function(response) {
                        vueStore.updateFormErrors([]);

                        // refresh page
                        sessionStorage.setItem('alerts', JSON.stringify(response.body.alerts));
                        window.location.reload();
                    }, function(response) {
                        vueStore.updateFormErrors(response.body);

                        if (response.body.alerts) {
                            response.body.alerts.forEach(function(alert) {
                                vm.$emit('add-alert', alert);
                            }.bind(this));
                        }

                        vm.$emit('hide-loader');
                    });

                }
            }
        });
    </script>
@endpush