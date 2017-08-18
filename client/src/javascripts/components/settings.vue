<template lang="pug">
section
    form.uk-form-stacked( v-if="formSettings")
        ul.uk-list.uk-list-divider( uk-accordion)
            li
                div.uk-accordion-title Email addresses to send form results to
                div.uk-accordion-content
                    p To email one person each form submission, enter their email in the field below.
                    div.uk-margin
                        label.uk-form-label To:
                        input.uk-input(
                            v-model="formSettings.toAddress"
                            type="text"
                        )

                    div.uk-margin( v-if="formItems")
                        hr
                        p Conditionally email an address based on the value of a field below.
                        div.uk-flex.uk-flex-middle( v-for="item in conditionalTo")
                            div.uk-card.uk-card-body.uk-card-small
                                span If field
                            div.uk-card.uk-card-body.uk-card-small
                                select.uk-select( v-model="item.field")
                                    option(
                                        v-for="formItem in formItems"
                                        v-if="formItem.type == 'select'"
                                    ) {{ formItem.label }}
                            div.uk-card.uk-card-body.uk-card-small( v-if="item.field")
                                span Has Value {{item.value}}
                            div.uk-card.uk-card-body.uk-card-small( v-if="item.field")
                                div(
                                    v-for="formItem in formItems"
                                    v-if="formItem.label == item.field"
                                )
                                    select.uk-select
                                        option( v-for="option in formItem.values") {{ option.value }}
                            div.uk-card.uk-card-body.uk-card-small( v-if="item.field")
                                div
                                    span Send email to
                            div.uk-card.uk-card-body.uk-card-small( v-if="item.field")
                                div
                                    input.uk-input(
                                        type="text"
                                        v-model="item.to"
                                    )
                    div
                        button.uk-button.uk-button-default.uk-button-primary(
                            @click.prevent="addConditionalTo"
                        ) Add
            li
                div.uk-accordion-title Subject line
                div.uk-accordion-content
                    section
                        div.uk-margin
                            label.uk-form-label Email Subject:
                            input.uk-input(
                                v-model="formSettings.subject"
                                type="text"
                            )

        div.uk-margin.uk-text-right
            button.uk-button.uk-button-default.uk-button-primary( @click.prevent="save()") Save
</template>

<script>
import axios from 'axios'
export default {
    name: 'Settings',
    props: ['formSettings', 'formItems'],
    mounted(){},
    data() {
        return {
            tos:[{
                to: null
            }],
            conditionalTo: [{
                field: null,
                to: null
            }]
        }
    },
    methods:{
        addConditionalTo(){
            this.conditionalTo.push({
                to: null ,
                field: null
            })
        },
        addTo(){
            this.tos.push({
                to: null
            })
        },
        save(){
            console.log(this.formSettings)
            let data = {
                id: this.$route.params.id,
                formSettings: JSON.stringify(this.formSettings)
            }

            axios.post('/admin/setup/dynamicforms/saveFormSettings', data).then((response) => {
                console.log(response.data)
            })
        }
    }
}
</script>

