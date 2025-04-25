<template>
	<div>
		<router-view :pageData="pageData"></router-view>
	</div>
</template>

<script>
	export default {
		name: 'MonthlyMainComponent',
        props: {
			pageData: {}
		},
		data() {
			return {

            }
		}
	}
</script>
