<template>

</template>


<script>

export default {
    components: {
    },
    data() {
        return {
            loading: true,
        }
    },
    methods: {
    },
    mounted() {
        this.$router.push({ name: this.firstPage });
    }
}
</script>
