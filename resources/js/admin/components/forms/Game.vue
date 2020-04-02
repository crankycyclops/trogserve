<template>

	<v-form ref="form">

		<v-text-field
			v-model.trim="nameModel"
			:counter="createMode ? nameMaxLen : false"
			:rules="createMode ? validation.name : []"
			:disabled="submitting || !createMode"
			label="Name"
			required
			outlined
		></v-text-field>

		<v-select
			v-model="definitionModel"
			:items="definitionList"
			:rules="createMode ? validation.definition : []"
			:disabled="submitting || !createMode"
			label="Definition"
			required
			outlined
		/>

		<v-text-field
			v-model.trim="titleModel"
			:counter="titleMaxLen"
			:rules="validation.title"
			:disabled="submitting"
			label="Title"
			outlined
		/>

		<v-text-field
			v-model.trim="authorModel"
			:counter="authorMaxLen"
			:rules="validation.author"
			:disabled="submitting"
			label="Author"
			outlined
		/>

		<v-textarea
			v-model.trim="synopsisModel"
			:counter="synopsisMaxLen"
			:rules="validation.synopsis"
			:disabled="submitting"
			label="Synopsis"
			outlined
		/>

		<v-switch
			v-model="autostartModel"
			:style="!createMode ? 'display: none;' : ''"
			:disabled="submitting || !createMode"
			class="ma-1"
			label="Autostart the game"
		/>

		<!-- Progress bar indicates when the form is being submitted -->
		<v-row align="center" justify="start" v-if="submitting">
			<v-col cols="12">
				<v-progress-linear :active="true" :indeterminate="true" />
			</v-col>
		</v-row>

	</v-form>

</template>

<script>

	export default {

		props: {

			name: {
				type: String
			},

			definition: {
				type: String
			},

			title: {
				type: String
			},

			author: {
				type: String
			},

			synopsis: {
				type: String
			},

			autostart: {
				type: Boolean,
				default: false
			},

			// List of definitions that can be selected
			definitionList: {
				type: Array,
				default: []
			},

			// We can either create a new game or edit an existing one
			createMode: {
				type: Boolean,
				default: false
			},

			// Whether or not the form is being submitted (toggle this in the
			// parent to show or hide the progress bar)
			submitting: {
				type: Boolean,
				default: false
			}
		},

		computed: {

			// Form validation values
			nameMaxLen: function () {return window.nameMaxLen;},
			titleMaxLen: function () {return window.titleMaxLen;},
			authorMaxLen: function () {return window.authorMaxLen;},
			synopsisMaxLen: function () {return window.synopsisMaxLen;},

			nameMaxLenMsg: function () {return window.nameMaxLenMsg;},
			titleMaxLenMsg: function () {return window.titleMaxLenMsg;},
			authorMaxLenMsg: function () {return window.authorMaxLenMsg;},
			synopsisMaxLenMsg: function () {return window.synopsisMaxLenMsg;},

			// These allow us to indirectly update the prop values
			nameModel: {

				get() {
					return this.name;
				},

				set(newValue) {
					this.$emit('update:name', newValue);
				}
			},

			definitionModel: {

				get() {
					return this.definition;
				},

				set(newValue) {
					this.$emit('update:definition', newValue);
				}
			},

			titleModel: {

				get() {
					return this.title;
				},

				set(newValue) {
					this.$emit('update:title', newValue);
				}
			},

			authorModel: {

				get() {
					return this.author;
				},

				set(newValue) {
					this.$emit('update:author', newValue);
				}
			},

			synopsisModel: {

				get() {
					return this.synopsis;
				},

				set(newValue) {
					this.$emit('update:synopsis', newValue);
				}
			},

			autostartModel: {

				get() {
					return this.autostart;
				},

				set(newValue) {
					this.$emit('update:autostart', newValue);
				}
			},
		},

		data: function () {

			return {

				// Validation rules for the form fields above
				validation: {

					name: [
						v => !!v || "You must give the game a name",
						v => (v || '').length <= this.nameMaxLen || this.nameMaxLenMsg
					],

					definition: [
						v => !!v || 'You must choose a game definition',
					],

					title:[
						v => (v || '').length <= this.titleMaxLen || this.titleMaxLenMsg
					],

					author: [
						v => (v || '').length <= this.authorMaxLen || this.authorMaxLenMsg
					],

					synopsis: [
						v => (v || '').length <= this.synopsisMaxLen || this.synopsisMaxLenMsg
					]
				}
			};
		},

		methods: {

			// Validate the form
			validate: function () {

				return this.$refs.form.validate();
			},

			// Reset the game form
			reset: function () {

				this.$refs.form.reset();
				this.$refs.form.resetValidation();
			}
		}
	};

</script>
