<template>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('directoryExhibitor'),
        'btn-success': isActive('directoryExhibitor'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('directoryExhibitor')">Directory</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('fasciaName'),
        'btn-success': isActive('fasciaName'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('fasciaName')">Fascia Name</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('productIndex'),
        'btn-success': isActive('productIndex'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('productIndex')">Product
        Index</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('badgesExhibitor'),
        'btn-success': isActive('badgesExhibitor'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('badgesExhibitor')">Exhibitor
        Badges</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('principalExhibitor'),
        'btn-success': isActive('principalExhibitor'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('principalExhibitor')">Exhibitor
        Principal</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('agentExhibitor'),
        'btn-success': isActive('agentExhibitor'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('agentExhibitor')">Exhibitor Agent</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('seekingAgentExhibitor'),
        'btn-success': isActive('seekingAgentExhibitor'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('seekingAgentExhibitor')">Exhibitor Seeking
        Agent</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('spaceStandExhibitor'),
        'btn-success': isActive('spaceStandExhibitor'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('spaceStandExhibitor')">Exhibitor Space
        Only Stand</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('furniturExhibitor'),
        'btn-success': isActive('furniturExhibitor'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('furniturExhibitor')">Exhibitor
        Furnitur</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('electricityExhibitor'),
        'btn-success': isActive('electricityExhibitor'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('electricityExhibitor')">Exhibitor
        Electricity</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('multimediaExhibitor'),
        'btn-success': isActive('multimediaExhibitor'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('multimediaExhibitor')">Exhibitor
        Multimedia</button>
    <button type="button" :class="{
        'btn': true,
        'btn-primary': !isActive('image'),
        'btn-success': isActive('image'),
    }" style="margin-left: 8px;margin-bottom: 8px;" @click="goToNavitagiton('image')">Product Image</button>
</template>

<script>
export default {
    methods: {
        isActive(routeName) {
            return this.$route.name === routeName;
        },
        goToNavitagiton(routeName) {
            this.$router.push({ name: routeName, params: { idexhibitor: this.$route.params.idexhibitor } });
        }
    },
};
</script>
