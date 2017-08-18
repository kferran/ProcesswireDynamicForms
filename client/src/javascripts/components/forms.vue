<template lang="pug">
section
    vue-good-table(
        :columns="columns"
        :rows="rows"
        :paginate="true"
        :lineNumbers="true"
        styleClass="uk-table uk-table-divider uk-table-striped uk-table-hover uk-table-small"
    )
        template( slot="table-row" scope="props")
            td
                router-link( :to="{ name: 'form', params: {id: props.row.id } }" ) Edit
            td {{ props.row.name }}
</template>

<script>
import axios from 'axios'
export default {
    name: 'Forms',
    data(){
        return {
            columns: [
                { label: 'Action', field: 'id'},
                { label: 'Form', field: 'name'}
            ],
            rows: [],
        }
    },
    mounted(){
        axios.get('/admin/setup/dynamicforms/getforms').then((response) => {
            // this.rows.push(response.data)
            response.data.forEach((item) => {
                this.rows.push(item)
            })
        })
    }
}
</script>

