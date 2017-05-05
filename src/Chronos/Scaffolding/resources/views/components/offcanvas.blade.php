<div class="offcanvas" v-bind:class="{ active: offcanvas }">
    <nav class="app-nav" role="navigation">
        <ul>
            @include('chronos::components.offcanvas_item', array('items' => $chronos_menu->roots()))
        </ul>
    </nav><!--/.nav-->
    <a href="#" class="quick-help">Help<span class="icon">?</span></a>
</div><!--/.offcanvas-->