<style>
/* contoh tambahkan ini di style scoped di komponen atau di css globalmu */
.loader-container {
    position: absolute;
    z-index: 999999;
    background: rgb(255 255 255 / 58%);
    width: 100vw;
    height: 100vh;
    text-align: -webkit-center;
    align-content: center;
    color: black;
}

.loader {
    width: 50px;
    aspect-ratio: 1;
    display: grid;
    border: 4px solid #0000;
    border-radius: 50%;
    border-color: #ccc #0000;
    animation: l16 1s infinite linear;
}

.loader::before,
.loader::after {
    content: "";
    grid-area: 1/1;
    margin: 2px;
    border: inherit;
    border-radius: 50%;
}

.loader::before {
    border-color: #f03355 #0000;
    animation: inherit;
    animation-duration: .5s;
    animation-direction: reverse;
}

.loader::after {
    margin: 8px;
}

@keyframes l16 {
    100% {
        transform: rotate(1turn)
    }
}
</style>
<template>
    <div class="loader-container" v-if="globalLoader.show">
        <div class="loader"></div>
    </div>
    <div id="wrapper">
        <div style="min-height: 94vh;">
            <router-view>
            </router-view>
        </div>

        <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i>
        </a>
        <landing-footer style="width: 100%;left: 0px;" v-if="this.$route.name !== 'notFound'"></landing-footer>
    </div>
</template>

<script>
import landingFooter from "../components/landing_footer.vue";


export default {
    components: {
        landingFooter
    },
};
</script>
