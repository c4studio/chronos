<nav class="text-center" v-show="!dataLoader && pagination.items > pagination.per_page">
    <ul class="pagination">
        <li>
            <a v-on:click="paginate(1)" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <li v-bind:class="{active: i == pagination.current}" v-for="i in pagination.last"><a v-on:click="paginate(i)">@{{ i }}</a></li>
        <li>
            <a v-on:click="paginate(pagination.last)" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>