<template lang="pug">
div
    transition( name="fade")
        router-view
</template>

<script>
export default {
    name: 'DynamicFormsApp',
}
</script>

<style lang="scss">
@import "./../../../node_modules/uikit/src/scss/variables-theme.scss";
@import "./../../../node_modules/uikit/src/scss/mixins-theme.scss";
@import "./../../../node_modules/uikit/src/scss/uikit-theme.scss";

#breadcrumbs ul{
  padding: 0;
  margin: 0;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity .5s
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0
  }
</style>
