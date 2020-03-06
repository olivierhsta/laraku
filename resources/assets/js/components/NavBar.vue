<template>
    <div class="navbar">
        <h1 class="logo m-l-30">Libre Sudoku</h1>
    </div>
</template>
<script>
    export default {
        name: "",
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
    }
</style>
