<template lang="pug">
section
    vue-good-table(
        title=""
        :columns="columns"
        :rows="rows"
        :paginate="true"
        :lineNumbers="true"
        styleClass="uk-table uk-table-divider uk-table-striped uk-table-hover uk-table-small"
    )
        template( slot="table-row" scope="props")
            td
                router-link(
                    to="/form"
                ) Edit
            td {{ props.row.name }}
</template>

<script>
export default {
    name: 'Entries',
     data(){
        return {
            searchTerm: '',
            onClickFn(){
                console.log('something');
            },
            columns: [
                {
                    label: 'Action',
                    field: 'id'
                },
                {
                    label: 'Form',
                    field: 'name',
                }
            ],
            rows: [
                {id:1, name:"Contact Us"},
                {id:1, name:"Volunteer"},
                {id:1, name:"Advocates"},
            ],
        }
    },
}
</script>

