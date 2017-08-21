<template lang="pug">
div
    ul.uk-list.uk-list-divider( uk-accordion)
        li( v-for="entry in formEntries")
            div.uk-accordion-title {{ entry.title }}
            div.uk-accordion-content
                section.uk-card
                    form.uk-form-stacked
                        div.uk-margin( v-for="item in entry.fields")
                            label.uk-form-label {{ item.label }}
                            div.uk-form-controls
                                input.uk-input(
                                    v-if="item.type != 'textarea'"
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
export default {
    name: 'Entries',
    props: ['formEntries'],
    data(){
        return {}
    },
}
</script>

