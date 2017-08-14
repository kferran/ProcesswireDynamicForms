<template lang="pug">
section
    form.uk-form-stacked( v-if="formSettings")
        div.uk-margin
            label.uk-form-label To: (who gets notified )
            input.uk-input(
                v-model="formSettings.toAddress"
                type="text"
            )
        div.uk-margin
            label.uk-form-label Email Subject
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
    props: ['formSettings', 'formFields'],
    mounted(){
    },
    methods:{
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

