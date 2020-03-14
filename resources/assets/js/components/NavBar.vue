<template>
    <div class="navbar">
        <h1 class="logo m-l-30">{{title}}</h1>
    </div>
</template>
<script>
    export default {
        props: ['title'],
        data: () => ({

        })
    }
</script>
<style lang="scss">
    @import './../../sass/_helpers.scss';
    .navbar {
        display: flex;
        align-items: center;
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: rem(50);
        background-color: $orange;
        color: white;

        .logo {
            font-weight: 300;
        }
    }
</style>
