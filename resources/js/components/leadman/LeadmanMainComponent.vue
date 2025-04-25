<template>
	<div>
		<router-view :pageData="pageData"></router-view>
	</div>
</template>

<script>
	export default {
		name: 'LeadmanMainComponent',
        props: {
			pageData: {}
		},
		data() {
			return {

            }
		}
	}
</script>
