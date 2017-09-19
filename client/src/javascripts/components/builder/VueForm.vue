<template lang="pug">
div
	form.uk-form.uk-form-stacked
		div.uk-grid
			div.uk-width-1-4
				draggable.uk-list.uk-list-divider(
					element="ul"
					:list="components"
					:options="componentsOptions"
					:clone="onClone"
				)
					li.collection-item( v-for="component in components")
						i(:class='getIcon(component.type)')
						|  {{component.label}}

			div.uk-width-3-4
				section
					draggable.uk-list.uk-list-divider(
						v-if="formFields"
						element='ul',
						data-collapsible='accordion',
						uk-accordion,
						:list='formFields',
						:class="['dropArea collapsible', !formFields.length ? 'empty' : '']",
						:options='formOptions',
						:move='onMove',
						@start='isDragging=true',
						@end='isDragging=false',
						data-content='Drag and drop your fields here'
					)
						li(
							v-for='(item, index) in formFields',
							:key='index'
						)
							div.uk-accordion-title.collapsible-header.uk-position-relative
								//- i(:class='getIcon(item.type)') &nbsp;
								i.fa.fa-trash-o.fa-lg.uk-position-absolute(
									style="right:35px; top:5px; margin:auto;"
									@click.stop='destroy(index)',
									aria-hidden='true'
								)
								|  {{item.label}}

							div.uk-accordion-content.collapsible-body
								div.uk-form.uk-form-stacked
									div.uk-margin.uk-grid-small.uk-child-width-auto.uk-grid
										label(:for="item.name + '-required'")
											input.uk-checkbox(
												type='checkbox',
												:id="item.name + '-required'",
												:checked='item.required',
												@click.stop='item.required = !item.required'
											)
											|  Required

									div.uk-margin
										label.uk-form-label(:for="item.name + '-label'") Label
										input.uk-input(:id="item.name + '-label'", type='text', v-model='item.label')

									div.uk-margin
										label.uk-form-label Width
										select.uk-select( v-model="item.width")
											option( v-for="width in widths" :value="width.value") {{ width.label }}

									div(v-if="['text', 'password', 'email'].includes(item.type)")
										div.uk-margin
											label.uk-form-label Type
											select.uk-select(v-model='item.type')
												option(v-for='(option, index) in optionType', :key='index', :value='option.value') {{option.label}}

									div(v-if="['checkbox-group', 'radio-group', 'select'].includes(item.type)")
										label.uk-form-label Values
										draggable.uk-card.uk-card-body.uk-background-muted(
											element='div',
											:list='item.values',
											:options="{group:{name:'item.values'}}"
										)
											div(
												class="uk-grid uk-child-width-1-2@s uk-child-width-1-3@m uk-flex-middle"
												v-for='(option, index) in item.values',
												:key='index'
											)
												div
													label.uk-form-label(:for="'option-label-' + index") Label
													input.uk-input(:id="'option-label-' + index", type='text', v-model='option.label')

												div
													label.uk-form-label(:for="'option-value-' + index") Value
													input.uk-input(:id="'option-value-' + index", type='text', v-model='option.value')

												div(v-if='item.values.length > 2')
													i.fa.fa-times(@click.stop='removeOption(item.values, index)')
										div.uk-margin
											a( @click.stop='addOption(item.values)') Add option

		div.uk-section.uk-section-small.uk-text-right
			button.uk-button.uk-button-default.uk-button-primary( @click.prevent="save()") Save Form

</template>

<script>
import draggable from 'vuedraggable'
import VueFormRender from './VueFormRender.vue'
import fieldTypes from './field-types'
import axios from 'axios'
import UIkit from 'uikit'
export default {
	name: 'vue-form',
	components: { draggable, VueFormRender },
	props: ['formFields'],
	data () {
		return {
			components: fieldTypes.components,
			widths: [
				{ 'label': '100%', 'value': 'uk-width-1-1'},
				{ 'label': '1/2', 'value': 'uk-width-1-2'},
				{ 'label': '1/3', 'value': 'uk-width-1-3'},
				{ 'label': '1/4', 'value': 'uk-width-1-4'},
			],
			optionType: [
			{
				label: "Text",
				value: "text",
			},
			{
				label: "Email",
				value: "email",
			},
			{
				label: "Password",
				value: "password",
			},
			],
			icons:{
				'checkbox': "fa fa-check-square-o fa-lg",
				'checkbox-group': "fa fa-check-square-o fa-lg",
				'text': "fa fa-pencil fa-lg",
				'textarea': "fa fa-pencil-square-o fa-lg",
				'select': 'fa fa-list fa-lg',
				'file': 'fa fa-file fa-lg',
				'radio-group': 'fa fa-dot-circle-o fa-lg',
			},
			isDragging: false,
			delayedDragging:false
		}
	},
	mounted(){
		// console.log(this.formFields)
		// if(this.formFields){
		// 	this.formFields = JSON.parse(this.formFields)
		// }
	},
	methods:{
		onMove ({relatedContext, draggedContext}) {
			const relatedElement = relatedContext.element;
			const draggedElement = draggedContext.element;
			return (!relatedElement || !relatedElement.fixed) && !draggedElement.fixed
		},
		onClone (el) {
			var input = JSON.parse(JSON.stringify(el));
			// input.name = input.type + '-' + Date.now();
			input.name = input.type + '-' + input.reference;
			input.fixed = false;
			input.required = false;
			input.description = '';

			return input
		},
		destroy (index) {
			UIkit.modal.confirm('Are you sure!').then(() =>  {
				this.formFields.splice(index, 1)
			})
		},
		addOption (values) {
			values.push({
				label: "Option "+ Date.now(),
				value: "option-"+ Date.now()
			})
		},
		removeOption (values, index) {
			values.splice(index, 1)
		},
		clearItems () {
			this.formFields =[];
		},
		getIcon(type) {
			return this.icons[type];
		},
		save(){
			axios.post('/admin/setup/dynamicforms/saveFormFields', { id: this.$route.params.id, formFields: this.listString }).then(() => {
				UIkit.modal.alert('Saved!')
			})
		}
	},
	computed: {
		componentsOptions () {
			return  {
				sort: false,
				group:{
					name:'components',
					pull:'clone',
					put:false
				}
			}
		},
		formOptions () {
			return  {
				handle: '.collapsible-header',
				group:{
					name:'components'
				}
			}
		},
		listString(){
			return JSON.stringify(this.formFields);
		}
	},
	watch: {
		isDragging (newValue) {
			if (newValue){
				this.delayedDragging= true
				return
			}
			this.$nextTick( () =>{
				this.delayedDragging =false
			})
		}
	}
}
</script>

<style lang="scss">
	.pw-content li{
		margin: 0;
	}

	.collection-item {
		cursor: move;
	}

	.dropArea {
		position: relative;
		min-height: 350px;
	}

	.dropArea.empty {
		opacity: 0.5;
		border: 1px dashed black;
		border-width: 3px;
		box-shadow: none;
	}

	.dropArea.empty:after {
		content: attr(data-content);
		position: absolute;
		text-align: center;
		top: 50%;
		left: 0;
		width: 100%;
	}
</style>
