<nav class="text-center" v-show="!dataLoader && pagination.items > pagination.per_page">
    <ul class="pagination">
        <li>
            <a v-on:click="paginate(pagination.current - 1)" aria-label="Previous">
                <span aria-hidden="true">&lsaquo;</span>
            </a>
        </li>
        <!-- Without ellipsis -->
        <li v-bind:class="{active: i == pagination.current}" v-for="i in pagination.last" v-if="pagination.last <= 7"><a v-on:click="paginate(i)">@{{ i }}</a></li>
        <!-- With ellipsis -->
        <li v-bind:class="{active: 1 == pagination.current}" v-if="pagination.last > 7"><a v-on:click="paginate(1)">1</a></li>
        <li class="ellipsis" v-if="pagination.current > 4 && pagination.last > 7"><span>&hellip;</span></li>
        <li v-for="i in 2" v-if="i - pagination.current + pagination.current * 2 - 3 > 1 && pagination.last > 7"><a v-on:click="paginate(i - pagination.current + pagination.current * 2 - 3)">@{{ i - pagination.current + pagination.current * 2 - 3 }}</a></li>
        <li class="active" v-if="pagination.current != 1 && pagination.current != pagination.last && pagination.last > 7"><a v-on:click="paginate(pagination.current)">@{{ pagination.current }}</a></li>
        <li v-for="i in 2" v-if="pagination.current + i < pagination.last && pagination.last > 7"><a v-on:click="paginate(pagination.current + i)">@{{ pagination.current + i }}</a></li>
        <li class="ellipsis" v-if="pagination.current < pagination.last - 3 && pagination.last > 7"><span>&hellip;</span></li>
        <li v-bind:class="{active: pagination.last == pagination.current}" v-if="pagination.last > 7"><a v-on:click="paginate(pagination.last)">@{{ pagination.last }}</a></li>
        <li>
            <a v-on:click="paginate(pagination.current + 1)" aria-label="Next">
                <span aria-hidden="true">&rsaquo;</span>
            </a>
        </li>
    </ul>
    <a class="display-block marginB30" v-on:click="showAll">{!! trans('chronos.content::interface.show all') !!}</a>
</nav>