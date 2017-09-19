<template lang="pug">
div
    p.uk-text-large.uk-margin-remove-top {{ payload.formTitle }}
    ul( uk-tab="animation: uk-animation-fade")
        li
            a( href="#") Form
        li
            a( href="#") Settings
        li
            a( href="#") Entries

    ul.uk-switcher
        li
            vue-form(
                v-if="payload.formFields"
                :form-fields="payload.formFields"
            )
        li
            settings(
                v-if="payload.formSettings"
                :form-settings="payload.formSettings", :form-fields="payload.formFields"
            )
        li
            entries(
                v-if="payload.formEntries"
                :form-entries="payload.formEntries"
            )
</template>

<script>

import VueForm from './builder/VueForm.vue'
import Entries from './entries'
import Settings from './settings'
import axios from 'axios'
export default {
    name: 'Form',
    components: { VueForm, Entries, Settings },
    data() {
        return {
            payload: {
                formFields: null,
                formSettings: null,
                formEntries: null,
            }
        }
    },
    mounted(){
        axios.post("/admin/setup/dynamicforms/getform?id=" + this.$route.params.id).then((response) => {
            this.payload.formTitle = response.data.formTitle
            this.payload.formFields = response.data.formFields
            this.payload.formSettings = response.data.formSettings
            this.payload.formEntries = response.data.formEntries
        })
    }
}
</script>

