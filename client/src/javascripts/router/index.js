import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)

import UIkit from 'uikit';
import Icons from 'uikit/dist/js/uikit-icons';
UIkit.use(Icons)

import Forms from './../components/forms.vue'
import Form from './../components/form.vue'

export default new Router({
	routes: [
		{
			path: '/',
			name: 'forms',
			component: Forms
		},
		{
			path: '/form/:id',
			name: 'form',
			component: Form
		}
	]
})
