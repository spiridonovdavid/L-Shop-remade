<template>
    <v-card>
        <v-card-title>
            {{ $t('content.admin.news.list.title') }}
            <v-btn flat color="primary" small icon :to="{name: 'admin.news.add'}"><v-icon>add</v-icon></v-btn>
            <v-spacer></v-spacer>
            <v-text-field
                    append-icon="search"
                    :label="$t('content.admin.news.list.search')"
                    single-line
                    hide-details
                    v-model="search"
            ></v-text-field>
        </v-card-title>
        <v-data-table
                :headers="headers"
                :items="items"
                :pagination.sync="pagination"
                :total-items="totalItems"
                :loading="loading"
                :no-data-text="$t('content.admin.news.list.table.empty')"
                :rows-per-page-items="[25, 50, 100]"
                :rows-per-page-text="$t('common.table.rows_per_page')"
                class="elevation-1"
        >
            <template slot="pageText" slot-scope="{ pageStart, pageStop }">
                {{ pageStart }} - {{ pageStop }} {{ $t('common.table.of') }} {{ totalItems }}
            </template>

            <template slot="items" slot-scope="props">
                <td class="text-xs-center">{{ props.item.id }}</td>
                <td class="text-xs-center">{{ props.item.title }}</td>
                <td class="text-xs-center">{{ props.item.user.username }}</td>
                <td class="text-xs-center">{{ props.item.created_at }}</td>
                <td class="justify-center layout px-0">
                    <v-btn icon class="mx-0" :to="{name: 'admin.news.edit', params: {news: props.item.id}}">
                        <v-icon color="secondary">edit</v-icon>
                    </v-btn>
                    <v-btn icon class="mx-0" @click="deleteNews(props.item)">
                        <v-icon color="pink">delete</v-icon>
                    </v-btn>
                </td>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
    import DateTime from './../../../core/common/datetime'

    export default {
        data() {
            return {
                search: '',
                totalItems: 0,
                items: [],
                loading: false,
                pagination: {
                    page: this.$route.query.page ? this.$route.query.page : 1,
                    rowsPerPage: this.$route.query.per_page ? parseInt(this.$route.query.per_page) : 25,
                    sortBy: this.$route.query.order_by ? this.$route.query.order_by : 'news.createdAt',
                    descending: this.$route.query.descending !== 'true',
                },
                headers: [
                    {
                        text: $t('content.admin.news.list.table.headers.id'),
                        align: 'center',
                        sortable: true,
                        value: 'news.id'
                    },
                    {
                        text: $t('content.admin.news.list.table.headers.title'),
                        align: 'center',
                        sortable: true,
                        value: 'news.title'
                    },
                    {
                        text: $t('content.admin.news.list.table.headers.username'),
                        sortable: true,
                        align: 'center',
                        value: 'user.username'
                    },
                    {
                        text: $t('content.admin.news.list.table.headers.created_at'),
                        sortable: true,
                        align: 'center',
                        value: 'news.createdAt'
                    },
                    {
                        text: $t('common.actions'),
                        align: 'center',
                        sortable: false
                    }
                ]
            }
        },
        watch: {
            pagination: {
                handler () {
                    let query = {};
                    if (this.pagination.page) {
                        query.page = this.pagination.page;
                    }
                    if (this.pagination.rowsPerPage) {
                        query.per_page = this.pagination.rowsPerPage;
                    }
                    if (this.pagination.sortBy) {
                        query.order_by = this.pagination.sortBy;
                    }
                    if (this.pagination.descending !== null) {
                        query.descending = this.pagination.descending;
                    }

                    this.$router.replace({name: 'admin.news.list', query: query}, () => {
                        this.retrieveFromApi();
                    }, () => {
                        this.retrieveFromApi();
                    });
                },
                deep: true
            }
        },
        methods: {
            retrieveFromApi() {
                this.loading = true;

                this.$axios.post('/spa/admin/news/list', {
                    page: this.$route.query.page,
                    per_page: this.$route.query.per_page,
                    order_by: this.$route.query.order_by,
                    descending: this.$route.query.descending,
                    search: this.search
                })
                    .then((response) => {
                        this.setTable(response.data)
                    });
            },
            deleteNews(news) {
                if (confirm($t('content.admin.news.list.delete'))) {
                    this.$axios.post(`/spa/admin/news/${news.id}`, {
                        _method: 'DELETE'
                    })
                        .then(response => {
                            if (response.data.status === 'success') {
                                this.items.forEach((each, index) => {
                                    if (each.id === news.id) {
                                        this.items.splice(index, 1);
                                    }
                                });
                            }
                        });
                }
            },
            setTable(data) {
                this.totalItems = data.paginator.total;
                this.items = data.news;
                this.items.forEach((item) => {
                    item.created_at = DateTime.localize(new Date(item.created_at));
                });

                this.loading = false;
            }
        }
    }
</script>
