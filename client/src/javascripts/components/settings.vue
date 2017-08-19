<template lang="pug">
section
    form.uk-form-stacked( v-if="model")
        ul.uk-list.uk-list-divider( uk-accordion)
            li
                div.uk-accordion-title Email addresses to send form results to
                div.uk-accordion-content
                    p To email one person each form submission, enter their email in the field below.
                    div.uk-margin
                        label.uk-form-label To:
                        input.uk-input( v-model="model.to" type="text")

                    div.uk-margin( v-if="formFields")
                        hr
                        p Conditionally email an address based on the value of a field below.
                        div.uk-flex.uk-flex-middle( v-for="item in model.conditionalTos")
                            div.uk-card.uk-card-body.uk-card-small
                                span If field
                            div.uk-card.uk-card-body.uk-card-small
                                select.uk-select( v-model="item.field")
                                    option( v-for="formItem in formFields" v-if="formItem.type == 'select'") {{ formItem.label }}

                            div.uk-card.uk-card-body.uk-card-small( v-if="item.field")
                                span Has Value {{item.value}}

                            div.uk-card.uk-card-body.uk-card-small( v-if="item.field")
                                div( v-for="formItem in formFields" v-if="formItem.label == item.field")
                                    select.uk-select( v-model="item.value")
                                        option( v-for="option in formItem.values") {{ option.value }}

                            div.uk-card.uk-card-body.uk-card-small( v-if="item.field")
                                div
                                    span Send email to

                            div.uk-card.uk-card-body.uk-card-small( v-if="item.field")
                                div
                                    input.uk-input( type="text" v-model="item.to")

                    div
                        button.uk-button.uk-button-default.uk-button-primary( @click.prevent="addConditionalTo") Add

            li
                div.uk-accordion-title Subject line
                div.uk-accordion-content
                    section
                        div.uk-margin
                            label.uk-form-label Email Subject:
                            input.uk-input(
                                v-model="model.subject"
                                type="text"
                            )

        div.uk-margin.uk-text-right
            button.uk-button.uk-button-default.uk-button-primary( @click.prevent="save()") Save
</template>

<script>
import axios from 'axios'
export default {
    name: 'Settings',
    props: ['formSettings', 'formFields'],
    data() {
        return {
            model: null
        }
    },
    mounted(){
        this.$nextTick(() =>  {
            this.model = this.formSettings ? this.formSettings : {
                subject: null,
                to: null,
                conditionalTos: [{
                    field: null,
                    to: null
                }]
            }
        });
    },
    methods:{
        addConditionalTo(){
            this.model.conditionalTos.push({
                to: null,
                field: null
            })
        },
        save(){

            // console.log(this.formSettings)
            let data = {
                id: this.$route.params.id,
                data: JSON.stringify(this.model)
            }

            axios.post('/admin/setup/dynamicforms/saveFormSettings', data).then((response) => {
                console.log(response.data)
            })
        }
    }
}
</script>

