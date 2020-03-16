<template>
    <div>
        <div class="row row-desktop">
            <sudoku-input
                class="col-desktop-7">
            </sudoku-input>
        </div>
        <div class="row row-desktop m-t-20">
            <sudoku-grid
                class="col-desktop-6 col-tablet-12 col-mobile-12"
                @moveMade="addLastMove"
            ></sudoku-grid>
            <control-pad
                @numberSelected="updateSelectedNumbers"
                @pencilmarksModeToggle="togglePencilMarks"
                @selectionModeToggle="toggleSelectionMode"
            ></control-pad>
        </div>
    </div>
</template>
<script>
export default {
    name: "",
    data() {
        return {
            numbersSelected: [],
            lastMoves: [],
            pencilMarksMode:false,
            selectionMode:false
        }
    },
    methods: {
        addLastMove(lm) {
            this.lastMoves.push(lm);
        },
        updateSelectedNumbers(ns) {
            this.numbersSelected = ns;
        },
        togglePencilMarks(pmm) {
            this.pencilMarksMode = pmm;
        },
        toggleSelectionMode(sm) {
            this.selectionMode = sm
        }
    },
}
</script>
<style lang="scss" scoped>
</style>
