<template lang="pug">
div
    form.uk-form-stacked( v-if="Settings")
        ul.uk-list.uk-list-divider( uk-accordion)
            li
                div.uk-accordion-title.uk-text-bold Email addresses to send form results to
                div.uk-accordion-content
                    section.uk-card.uk-card-body.uk-card-small.uk-background-muted
                        p To email one person each form submission, enter their email in the field below.
                        div
                            label.uk-form-label To:
                            input.uk-input( v-model="Settings.to" type="text")
                    hr

                    section.uk-card.uk-card-body.uk-card-small.uk-background-muted( v-if="hasSelect")
                        p Conditionally email an address based on the value of a select field below.
                        div.uk-overflow-auto
                            table.uk-table.uk-table-divider.uk-table-justify.uk-table-middle.uk-table-small.uk-table-hover
                                tr( v-for="(item, index) in Settings.conditionalTos")
                                    td If field
                                    td
                                        select.uk-select( v-model="item.field")
                                            option( v-for="formItem in formFields" v-if="formItem.type == 'select'") {{ formItem.label }}
                                    td( v-if="item.field") Has Value
                                    td( v-if="item.field")
                                        div( v-for="formItem in formFields" v-if="formItem.label == item.field")
                                            select.uk-select( v-model="item.value")
                                                option( v-for="option in formItem.values") {{ option.value }}
                                    td( v-if="item.field") Send email to
                                    td( v-if="item.field")
                                        input.uk-input( type="text" v-model="item.to")
                                    td
                                        button.uk-button.uk-button-default.uk-button-danger.uk-button-small(
                                            @click.prevent='deleteConditionalTo(item, index)',
                                        ) delete

                    div
                        button.uk-button.uk-button-default.uk-button-primary( @click.prevent="addConditionalTo") Add

            li
                div.uk-accordion-title.uk-text-bold Email from field
                div.uk-accordion-content

                    section.uk-card.uk-card-body.uk-card-small.uk-background-muted
                        span.uk-text-small.uk-text-bold( v-if="!hasEmailType") There needs to be a text field with the type set to email for the conditonal from logic to work
                        div.uk-margin( v-if="hasEmailType")
                            p When emailing a form submission to you, we can optionally make the "From" line contain an email address entered in the form (assuming you have an Email field present). Select which field will contain the submitter's email address.
                            select.uk-select(
                                v-model="Settings.conditionalFrom.field"
                            )
                                option( value="")
                                option(
                                    v-for="formItem in formFields"
                                    v-if="formItem.type == 'email'"
                                ) {{ formItem.label }}

                    hr
                    section.uk-card.uk-card-body.uk-card-small.uk-background-muted
                        p If no conditions apply to who emails should be from, fill in the field below.
                        div
                            label.uk-form-label From:
                            input.uk-input(
                                v-model="Settings.from"
                                @input="Settings.conditionalFrom.field = null"
                                type="text"
                            )

            li
                div.uk-accordion-title.uk-text-bold Subject line
                div.uk-accordion-content
                    section.uk-card.uk-card-body.uk-card-small.uk-background-muted
                        p Conditionally set the subject of this email.
                            span( v-if="hasConditionalSubjectAbility")  formatting will be "Selected Subject (email address)"
                        span.uk-text-small.uk-text-bold( v-if="!hasConditionalSubjectAbility") There needs to be a text field with the type set to email and a select field in order for the conditonal subject logic to work

                        div( v-if="hasConditionalSubjectAbility")
                            select.uk-select( v-model="Settings.conditionalSubject")
                                option( v-for="formItem in formFields" v-if="formItem.type == 'select'") {{ formItem.label }}

                    hr
                    section.uk-card.uk-card-body.uk-card-small.uk-background-muted
                        p Set the default subject of this email.
                        div
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
    computed:{
        hasEmailType(){
            return this.formFields.filter(field => field.type == 'email').length
        },
        hasSelect(){
            return this.formFields.filter(field => field.type == 'select').length
        },
        hasConditionalSubjectAbility(){
            // If has a select field
            // if has a field with type of email
            let hasSelect = this.formFields.filter(field => field.type == 'select')
            let hasEmail = this.formFields.filter(field => field.type == 'email')

            if(hasSelect.length && hasEmail.length){
                return true
            }

            return false
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
        deleteConditionalTo(item, index){
            UIkit.modal.confirm('Are you sure!').then(() =>  {
                this.Settings.conditionalTos.splice(index, 1)
                this.save()
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

