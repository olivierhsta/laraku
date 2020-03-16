<template>
    <div class="row row-desktop">
        <div class="control-grid num-pad row row-mobile row-tablet">
            <div class="control-cell button dark simple" :class="{'cell-selected':num.isSelected}"
                 v-for="num in controlNums" :key="num.value"
                 @click="numberClicked(num)">
                {{num.value}}
            </div>
        </div>
        <div class="control-grid action-pad row row-mobile row-tablet">
            <div class="control-cell button dark simple" :class="{'cell-selected':pencilMarkMode}" alt="Pencil Mark" title="Pencil Mark" @click="pencilMarkClicked()">
                <svg-vue icon="edit" class="control-icon"></svg-vue>
                <!-- <div>Icons made by <a href="https://www.flaticon.com/authors/kiranshastry" title="Kiranshastry">Kiranshastry</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div> -->
            </div>
            <div class="control-cell button dark simple" alt="Undo" title="Undo" @click="undoClicked()">
                <svg-vue icon="undo" class="control-icon"></svg-vue>
                <!-- Icons made by <a href="https://www.flaticon.com/authors/bqlqn" title="bqlqn">bqlqn</a> from <a href="https://www.flaticon.com/" title="Flaticon"> www.flaticon.com</a> -->
            </div>
            <div class="control-cell button dark simple" :class="{'cell-selected':selectionMode}" alt="Select" title="Select" @click="selectionClicked()">
                <svg-vue icon="touch" class="control-icon"></svg-vue>
                <!-- <div>Icons made by <a href="https://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div> -->
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data: () => ({
            controlNums : [
                {isSelected:false, value:1},
                {isSelected:false, value:2},
                {isSelected:false, value:3},
                {isSelected:false, value:4},
                {isSelected:false, value:5},
                {isSelected:false, value:6},
                {isSelected:false, value:7},
                {isSelected:false, value:8},
                {isSelected:false, value:9}
            ],
            selectionMode:false,
            pencilMarkMode:false
        }),
        computed: {

        },
        methods: {
            unselectAllNumbers() {
                this.controlNums.forEach(num => {
                    num.isSelected = false;
                });
            },
            numberClicked(num) {
                if (!this.selectionMode) {
                    this.unselectAllNumbers();
                }
                num.isSelected = num.isSelected ? false : true;
                this.$emit('numberSelected', this.getSelectedNumbers());
            },
            pencilMarkClicked() {
                this.pencilMarkMode = this.pencilMarkMode ? false : true;
                this.$emit('pencilmarksModeToggle', this.pencilMarkMode);
            },
            undoClicked() {
                this.$root.$emit('undo');
            },
            selectionClicked() {
                this.unselectAllNumbers();
                this.selectionMode = this.selectionMode ? false : true;
                this.$emit('selectionModeToggle', this.selectionMode);
            },
            getSelectedNumbers() {
                let selectedNums = [];
                this.controlNums.forEach(num => {
                    if (num.isSelected == true) {
                        selectedNums.push(num.value);
                    }
                });
                return selectedNums;
            }
        },
    }
</script>
<style lang="scss">
    @import './../../sass/_helpers.scss';

    .control-grid {
        display: flex;

        @include only(desktop) {
            height: rem(570);
            flex-direction: column;

        }
        @include below(desktop) {
            width: rem(570);
        }

        .control-cell {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: rem(30);
            font-weight: 700;
            margin:rem(1);
            width:rem(60);
            height: 100%;

            @include below(desktop) {
                height:rem(60);
                line-height:rem(60);
                width:100%;
            }

            &.cell-selected {
                background: $alpha !important;
            }

            .control-icon {
                width: rem(30);
                padding: rem(1) 0;
            }
        }
    }
</style>
