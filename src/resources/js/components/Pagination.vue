<style lang="scss">
    .pagination {
        justify-content: flex-end !important;
        .page-stats {
            align-items: center;
            margin-right: 5px;
        }
        i {
            color: #3273dc !important;
        }
    }
    .page-item {
      cursor: pointer;
    }
</style>

<template>
    <nav v-if="!client">
        <span class="page-stats">{{pagination.from}} - {{pagination.to}} of {{pagination.total}}</span>
        <ul class="pagination">
            <li class="page-item" v-if="pagination.prevPageUrl">
                <a class="page-link" @click="$emit('prev');">
                    Prev
                </a>
            </li>
            <li class="page-item" v-else>
                <a class="page-link"  :disabled="true">
                    Prev
                </a>
            </li>
            <li class="page-item" v-if="pagination.nextPageUrl">
                <a  class="page-link" @click="$emit('next');">
                    Sig
                </a>
            </li>
            <li class="page-item" v-else>
                <a class="page-link"  :disabled="true">
                    Sig
                </a>
            </li>
        </ul>

    </nav>

    <nav v-else>
        <span class="page-stats">
            {{pagination.from}} - {{pagination.to}} of {{filtered.length}}
            <span v-if="filtered.length < pagination.total">(filtered from {{pagination.total}} total entries)</span>
        </span>

        <ul class="pagination">
            <li class="page-item" v-if="pagination.prevPage">
                <a  class="page-link" @click="$emit('prev');">
                    Prev
                </a>
            </li>
            <li class="page-item" v-else>
                <a class="page-link"  :disabled="true">
                    Prev
                </a>
            </li>
            <li class="page-item" v-if="pagination.nextPage">
                <a  class="page-link" @click="$emit('next');">
                    Sig
                </a>
            </li>
            <li class="page-item" v-else>
                <a class="page-link"  :disabled="true">
                    Sig
                </a>
            </li>

        </ul>
    </nav>
</template>

<script>
    export default {
        props: ['pagination', 'client', 'filtered']
    }
</script>
