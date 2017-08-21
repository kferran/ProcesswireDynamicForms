import Vue from 'vue'
import VueGoodTable from 'vue-good-table';
Vue.use(VueGoodTable);

import UIkit from 'uikit';
import Icons from 'uikit/dist/js/uikit-icons';
UIkit.use(Icons)

import App from './components/app.vue'
import router from './router'

setTimeout(() => {
    new Vue({
      el: '#dynamic-forms',
      router,
      render: h => h(App)
    })
}, 100)