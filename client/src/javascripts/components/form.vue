<template lang="pug">
section
    ul( uk-tab)
        li
            a( href="#") Settings
        li
            a( href="#") Form
        li
            a( href="#") Entries


    ul.uk-switcher
        li
            settings( :form-settings="payload.formSettings", :form-fields="payload.formFields")
        li
            vue-form( :form-items="payload.formFields")
        li
            entries
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
                formSettings: null
            }
        }
    },
    mounted(){
        axios.post("/admin/setup/dynamicforms/getform?id=" + this.$route.params.id).then((response) => {
            this.payload.formFields = JSON.parse(response.data.formFields)
            this.payload.formSettings = JSON.parse(response.data.formSettings)
        })
    }
}
</script>

