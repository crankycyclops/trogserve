<script>

	export default {

		methods: {

			// Capitalize the first letter of a string (used primarily for
			// API error messages that are all lowercase.)
			capitalize: string => string.charAt(0).toUpperCase() + string.substring(1),

			// Extracts the error message from an axios error object
			getResponseError(error) {

				if ('undefined' !== typeof(error.response)) {
					return this.capitalize(error.response.data.error ? error.response.data.error : error.response.data.message);
				} else {
					return this.capitalize(error.message);
				}
			}
		}
	};

</script>
