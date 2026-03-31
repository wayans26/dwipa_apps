<template>
    <div v-if="validationErrors" class="alert alert-danger">
        <ul>
            <li v-for="(item, index) in validationErrors" :key="index">
                {{ item }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ["errors"],
    data() {
        return {};
    },
    computed: {
        validationErrors() {
            return Object.values(this.errors).flat();
        },
    },
};
</script>
