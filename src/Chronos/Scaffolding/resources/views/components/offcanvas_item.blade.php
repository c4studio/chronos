@foreach($items as $item)
    @if (is_null($item->permissions) || Auth::user()->hasOneOfPermissions($item->permissions))
    <li@lm-attrs($item) v-bind:class="{ open: isOffcanvasOpen('{{ $item->nickname }}') }" @lm-endattrs>
        <a @if ($item->url()) href="{!! $item->url() !!}" @else v-on:click="toggleOffcanvasOpen('{{ $item->nickname }}')" @endif>{!! $item->title !!}</a>
        @if ($item->hasChildren())
            <ul>
                @include('chronos::components.offcanvas_item', array('items' => $item->children()))
            </ul>
        @endif
    </li>
    @endif
@endforeach