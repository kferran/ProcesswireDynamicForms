<template lang="pug">
div
	ul( uk-tab="animation: uk-animation-fade")
		li
			a( href="#") Forms
		li
			a( href="#") New Form

	ul.uk-switcher
		li
			vue-good-table(
				:columns="columns"
				:rows="rows"
				:paginate="true"
				:lineNumbers="true"
				styleClass="uk-table uk-table-divider uk-table-striped uk-table-hover uk-table-small"
			)
				template( slot="table-row" scope="props")
					td
						router-link.uk-button.uk-button-link( :to="{ name: 'form', params: {id: props.row.id } }" ) Edit
						| &nbsp;
						button.uk-button.uk-button-link(
							type="button"
							@click.prevent="deleteForm(props.row.id)"
						 ) Delete
					td {{ props.row.name }}
		li
			section
				form.uk-form-stacked
					div.uk-margin
						label.uk-form-label Form Name
						input.uk-input(
							type="text"
							v-model="form.formName"
						)
					div.uk-margin
						button.uk-button.uk-button-default.uk-button-primary( @click.prevent="save()") Save Form

</template>

<script>
import UIkit from 'uikit'
import axios from 'axios'
export default {
	name: 'Forms',
	data(){
		return {
			form: {
				formName: null,
			},
			columns: [
				{ label: 'Action', field: 'id'},
				{ label: 'Form', field: 'name'}
			],
			rows: [],
		}
	},
	mounted(){
		axios.get('/admin/setup/dynamicforms/getforms').then((response) => {
			response.data.forEach(item => this.rows.push(item))
		})
	},
	methods: {
		save(){
			axios.post('/admin/setup/dynamicforms/saveForm', { form: this.form}).then((response) => {
				UIkit.modal.alert('Created form!')
				this.$router.push({ name: 'form', params: { id: response.data.formId }})
			})
		},
		deleteForm(formId){
			UIkit.modal.confirm('Are you sure!').then(() =>  {
				axios.post('/admin/setup/dynamicforms/deleteForm', { formId: formId}).then((response) => {
					window.location.reload()
				})
			})
		}
	}
}
</script>

