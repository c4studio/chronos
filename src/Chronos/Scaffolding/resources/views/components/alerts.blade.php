<div class="alerts-wrapper">
    @if (Session::has('alerts'))
        @foreach (Session::get('alerts') as $alert)
            <alert alert-type="{{ $alert->type }}" alert-title="{{ $alert->title }}" alert-message="{{ $alert->message }}"></alert>
        @endforeach
    @endif
    <alert v-for="(alert, key) in alerts" v-bind:key="key" v-bind:alert-type="alert.type" v-bind:alert-title="alert.title" v-bind:alert-message="alert.message"></alert>
</div><!--/.alerts-wrapper -->