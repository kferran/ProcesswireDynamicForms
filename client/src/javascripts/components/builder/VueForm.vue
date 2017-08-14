<template lang="pug">
div
	form.uk-form.uk-form-stacked
		div.uk-grid
			div.uk-width-1-4
				div.uk-card.uk-card-default.uk-card-small
					div.uk-card-body
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
				section.uk-card.uk-card-body.uk-card-small.uk-card-default
					draggable.uk-list.uk-list-divider(
						v-if="formItems"
						element='ul',
						data-collapsible='accordion',
						uk-accordion,
						:list='formItems',
						:class="['dropArea collapsible', !formItems.length ? 'empty' : '']",
						:options='formOptions',
						:move='onMove',
						@start='isDragging=true',
						@end='isDragging=false',
						data-content='Drag and drop your fields here'
					)
						li(
							v-for='(item, index) in formItems',
							:key='index'
						)
							div.uk-accordion-title.collapsible-header
								i(:class='getIcon(item.type)')
								|  {{item.label}}
								i.fa.fa-trash-o.fa-lg(
									@click.stop='destroy(index)',
									aria-hidden='true'
								)

							div.uk-accordion-content.collapsible-body
								div.uk-form.uk-form-stacked
									div.uk-margin.uk-grid-small.uk-child-width-auto.uk-grid
										label(:for="item.name + '-required'")
											input.uk-checkbox(type='checkbox', :id="item.name + '-required'", :checked='item.required', @click.stop='item.required = !item.required')
											|  Required
									div.uk-margin
										label.uk-form-label(:for="item.name + '-label'") Label
										input.uk-input(:id="item.name + '-label'", type='text', v-model='item.label')

									div(v-if="['text', 'password', 'email'].includes(item.type)")
										div.uk-margin
											label.uk-form-label Type
											select.uk-select(v-model='item.type')
												option(v-for='(option, index) in optionType', :key='index', :value='option.value') {{option.label}}

										div.uk-margin
											label.uk-form-label Width
											div.uk-margin.uk-grid-small.uk-child-width-auto.uk-grid
												label
													input.uk-radio(
														type="radio"
														:name="item.name+ '-width'"
														value="uk-width-1-1"
														v-model="item.width"
													)
													| 100%
												label
													input.uk-radio( type="radio" :name="item.name+ '-width'" value="uk-width-1-2" v-model="item.width")
													| 50%

									div(v-if="['checkbox-group', 'radio-group', 'select'].includes(item.type)")
										label.uk-form-label Values
										draggable.uk-card.uk-card-body.uk-background-muted(element='div', :list='item.values', :options="{group:{name:'item.values'}}" )
											div(
												class="uk-grid uk-child-width-1-2@s uk-child-width-1-3@m"
												v-for='(option, index) in item.values',
												:key='index'
											)
												div
													label.uk-form-label(:for="'option-label-' + index") Label
													input.uk-input(:id="'option-label-' + index", type='text', v-model='option.label')

												div
													label.uk-form-label(:for="'option-value-' + index") Value
													input.uk-input(:id="'option-value-' + index", type='text', v-model='option.value')

												div.uk-margin(v-if='item.values.length > 2')
													i.fa.fa-times(@click.stop='removeOption(item.values, index)')
										div.uk-margin
											a( @click.stop='addOption(item.values)') Add option


		div.uk-section.uk-section-small.uk-text-right
			a.uk-button.uk-button-default.uk-button-danger.uk-margin-small-right( @click.stop="clearItems()") Clear
			button.uk-button.uk-button-default.uk-button-primary( @click.prevent="save()") Save

</template>

<script>
import draggable from 'vuedraggable'
import VueFormRender from './VueFormRender.vue'
import fieldTypes from './field-types'
import axios from 'axios'
export default {
	name: 'vue-form',
	components: { draggable, VueFormRender },
	props: ['formItems'],
	data () {
		return {
			components: fieldTypes.components,
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
				'text': "fa fa-pencil fa-lg",
				'textarea': "fa fa-pencil-square-o fa-lg",
				'select': 'fa fa-list fa-lg',
			},
			isDragging: false,
			delayedDragging:false
		}
	},
	mounted(){
		// console.log(this.formFields)
		// if(this.formFields){
		// 	this.formItems = JSON.parse(this.formFields)
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
			input.name = input.type + '-' + Date.now();
			input.fixed = false;
			input.required = false;
			input.description = '';

			return input
		},
		destroy (index) {
			this.formItems.splice(index, 1)
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
			this.formItems =[];
		},
		getIcon(type) {
			return this.icons[type];
		},
		save(){
			axios.post('/admin/setup/dynamicforms/saveFormFields', { id: this.$route.params.id, formFields: this.listString }).then((response) => {
				console.log(response.data)
			})
			console.log(this.listString)
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
			return JSON.stringify(this.formItems);
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
		min-height: 440px;
		padding: 10px 0;
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

	.uk-accordion-title{


	}
</style>
