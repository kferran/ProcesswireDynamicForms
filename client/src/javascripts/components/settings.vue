<template lang="pug">
div
    form.uk-form-stacked( v-if="Settings")
        ul.uk-list.uk-list-divider( uk-accordion)
            li
                div.uk-accordion-title Email addresses to send form results to
                div.uk-accordion-content

                    section
                        p To email one person each form submission, enter their email in the field below.
                        div
                            label.uk-form-label To:
                            input.uk-input( v-model="Settings.to" type="text")
                    hr

                    section
                        p Conditionally email an address based on the value of a select field below.
                        div.uk-flex.uk-flex-middle( v-for="item in Settings.conditionalTos")
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
                div.uk-accordion-title Email from field
                div.uk-accordion-content
                    section
                        p When emailing a form submission to you, we can optionally make the "From" line contain an email address entered in the form (assuming you have an Email field present). Select which field will contain the submitter's email address.
                        div.uk-margin
                            select.uk-select( v-model="Settings.from")
                                option( v-for="formItem in formFields") {{ formItem.label }}

                    hr
                    section
                        p If no conditions apply to who emails should be from, fill in the field below.
                        div
                            label.uk-form-label From:
                            input.uk-input( v-model="Settings.from" type="text")

            li
                div.uk-accordion-title Subject line
                div.uk-accordion-content
                    section
                        label.uk-form-label Email Subject:
                        input.uk-input(
                            v-model="Settings.subject"
                            type="text"
                        )

        div.uk-section.uk-text-right
            button.uk-button.uk-button-default.uk-button-primary( @click.prevent="save()") Save Settings
</template>

<script>
import axios from 'axios'
import UIkit from 'uikit'
import Settings from './../entities/Settings'
export default {
    name: 'Settings',
    props: ['formSettings', 'formFields'],
    data() {
        return {
            Settings: Settings
        }
    },
    mounted(){
        this.$nextTick(() =>  {
            this.Settings.populate(this.formSettings)
        });
    },
    methods:{
        addConditionalTo(){
            this.Settings.conditionalTos.push({
                to: null,
                field: null
            })
        },
        save(){
            let data = {
                formId: this.$route.params.id,
                data: JSON.stringify(this.Settings)
            }

            axios.post('/admin/setup/dynamicforms/saveFormSettings', data).then((response) => {
                UIkit.modal.alert('Saved!')
            })
        }
    }
}
</script>

