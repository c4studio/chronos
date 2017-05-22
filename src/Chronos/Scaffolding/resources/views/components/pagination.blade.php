<nav class="text-center" v-show="!dataLoader && pagination.items > pagination.per_page">
    <ul class="pagination">
        <li>
            <a v-on:click="paginate(1)" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <!-- Without ellipsis -->
        <li v-bind:class="{active: i == pagination.current}" v-for="i in pagination.last" v-if="pagination.last <= 6"><a v-on:click="paginate(i)">@{{ i }}</a></li>
        <!-- With ellipsis -->
        <li v-bind:class="{active: i == pagination.current}" v-for="i in pagination.last" v-if="i <= 3 && pagination.last > 6"><a v-on:click="paginate(i)">@{{ i }}</a></li>
        <li class="ellipsis" v-if="pagination.last > 6"><span>&hellip;</span></li>
        <li v-bind:class="{active: i == pagination.current}" v-for="i in pagination.last" v-if="i > pagination.last - 3 && pagination.last > 6"><a v-on:click="paginate(i)">@{{ i }}</a></li>
        <li>
            <a v-on:click="paginate(pagination.last)" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>