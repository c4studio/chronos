<nav class="text-center" v-show="!dataLoader && pagination.items > pagination.per_page">
    <ul class="pagination">
        <li>
            <a v-on:click="paginate(pagination.current - 1)" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <!-- Without ellipsis -->
        <li v-bind:class="{active: i == pagination.current}" v-for="i in pagination.last" v-if="pagination.last <= 7"><a v-on:click="paginate(i)">@{{ i }}</a></li>
        <!-- With ellipsis -->
        <li v-bind:class="{active: i == pagination.current}" v-for="i in pagination.last" v-if="i <= 3 && pagination.last > 7"><a v-on:click="paginate(i)">@{{ i }}</a></li>
        <li class="ellipsis" v-if="pagination.current > 4  && pagination.current <= pagination.last - 3 && pagination.last > 7"><span>&hellip;</span></li>
        <li class="active" v-if="pagination.current > 3 && pagination.current <= pagination.last - 3 && pagination.last > 7"><a v-on:click="paginate(pagination.current)">@{{ pagination.current }}</a></li>
        <li class="ellipsis" v-if="pagination.current != pagination.last - 3 && pagination.last > 7"><span>&hellip;</span></li>
        <li v-bind:class="{active: i == pagination.current}" v-for="i in pagination.last" v-if="i > pagination.last - 3 && pagination.last > 7"><a v-on:click="paginate(i)">@{{ i }}</a></li>
        <li>
            <a v-on:click="paginate(pagination.current + 1)" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
    <a class="display-block marginB30" v-on:click="showAll">{!! trans('chronos.content::interface.show all') !!}</a>
</nav>