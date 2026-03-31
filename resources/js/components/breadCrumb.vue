<template>
    <ol class="breadcrumb" v-if="globalBreadcrumb !== ''">
        <li class="breadcrumb-item"><a href="javaScript:void(0);">{{ globalBreadcrumb.menu_group }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ globalBreadcrumb.menu_sub_group }}</li>
    </ol>
</template>
