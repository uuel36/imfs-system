<template>
	<div>
		<router-view :pageData="pageData"></router-view>
	</div>
</template>

<script>
	export default {
		name: 'LinearMainComponent',
        props: {
			pageData: {}
		},
		data() {
			return {

            }
		}
	}
</script>
