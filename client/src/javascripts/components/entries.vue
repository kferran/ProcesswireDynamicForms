<template lang="pug">
div
    ul.uk-list.uk-list-divider( uk-accordion)
        li( v-for="(entry, index) in formEntries")
            div.uk-accordion-title.uk-position-relative
                | {{ entry.title }} - {{ entry.created }}
                i.fa.fa-trash-o.fa-lg.uk-position-absolute(
                    style="right:35px; top:5px; margin:auto;"
                    @click.prevent='deleteEntry(entry, index)',
                )
            div.uk-accordion-content
                section.uk-card
                    form.uk-form-stacked
                        div.uk-margin( v-for="item in entry.fields")
                            label.uk-form-label {{ item.label }}
                            div.uk-form-controls
                                div( v-if="item.type == 'checkbox-group'")
                                    label.uk-margin-small-right( v-for="option in item.values")
                                        input.uk-checkbox(
                                            type="checkbox"
                                            :value="option.value"
                                            v-model="option.selected"
                                        )
                                        |  {{ option.label }}

                                div( v-if="item.type == 'radio-group'")
                                    label.uk-margin-small-right( v-for="option in item.values")
                                        input.uk-radio(
                                            type="radio"
                                            :value="option.value"
                                            v-model="option.selected"
                                        )
                                        |  {{ option.label }}


                                input.uk-input(
                                    v-if="['text','checkbox', 'select'].includes(item.type ) "
                                    :value="item.value"
                                    disabled
                                )
                                textarea.uk-textarea(
                                    v-if="item.type == 'textarea'"
                                    :value="item.value"
                                    rows="5"
                                    disabled
                                )
</template>

<script>
import axios from 'axios'
import UIkit from 'uikit'
export default {
    name: 'Entries',
    props: ['formEntries'],
    data(){
        return {}
    },
    methods: {
        deleteEntry(entry, index){
            UIkit.modal.confirm('Are you sure!').then(() =>  {
                axios.post('/admin/setup/dynamicforms/deleteFormEntry', { entryId: entry.id}).then((response) => {
                    UIkit.accordion('.uk-accordion').toggle(index, false)
                    this.formEntries.splice(index, 1)
                    UIkit.modal.alert('Deleted!')
                })
            })
        }
    }
}
</script>

