<template>
<table
    class="table table-striped table-bordered"
>
    <thead>
        <tr>
            <th v-for="column in columns" :key="column.name" @click="$emit('sort', column.name)"
                :class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"
                style="cursor: pointer;"
                >
                <b-row>
                    <b-col>
                        {{column.label}}
                    </b-col>
                    <b-col>
                        <b-icon-arrow-down v-if="sortKey === column.name && sortOrders[column.name] > 0"></b-icon-arrow-down>
                        <b-icon-arrow-up v-else-if="sortKey === column.name && sortOrders[column.name] < 0"></b-icon-arrow-up>
                        <b-icon-arrow-down-up v-else="sortKey !== column.name"></b-icon-arrow-down-up>
                    </b-col>
                </b-row>
            </th>
            <th>
                Acciones
            </th>
        </tr>
    </thead>
    <slot></slot>
</table>

</template>

<script>
    export default {
        props: ['columns', 'sortKey', 'sortOrders']
    }
</script>
