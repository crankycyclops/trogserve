<template>

    <transition name="showMessage">
        <v-row align="center" justify="start" v-show="message.length">
            <v-col cols="12">
                <span :class="type">{{ message }}</span>
            </v-col>
        </v-row>
    </transition>

</template>


<style scoped>

	.showMessage-enter,
	.showMessage-leave-to {
		visibility: hidden;
		height: 0;
		margin: 0;
		padding: 0;
		opacity: 0;
	}

	.showMessage-enter-active,
	.showMessage-leave-active {
		transition: all 0.3s;
	}

</style>


<script>

	export default {

		props: {
            type: String,
            message: String
		}
	};

</script>