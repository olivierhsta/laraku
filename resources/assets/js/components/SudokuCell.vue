<template>
    <td class="sudoku-cell"
        :class="{'selected':isSelected,'sudoku-border-right-md': isBorderedRight(col),'sudoku-border-bottom-md': isBorderedBottom(row)} "
        @click="click()">
        <table v-if="hasPencilMarks()"
               class="sudoku-pencil-marks">
            <tr v-for="i in 3" :key="i">
                <td v-for="j in 3" class="p-0" :key="j">
                    <small v-if="hasPencilMark((i-1)*3+j)">
                        {{ (i-1)*3+j }}
                    </small>
                    <small v-else class="hidden">
                        0
                    </small>
                </td>
            </tr>
        </table>
        <span v-else-if="hasValue()" class="sudoku-cell-value" v-text="value"></span>
    </td>
</template>
<script>
export default {
    props:['row','col','content'],
    name: "",
    data: () => ({
        pencilMarks:[],
        value:null,
        isSelected:false,
    }),
    methods: {
        construct() {
            this.isSelected = false;
            if (Array.isArray(this.content)) {
                this.pencilMarks = [];
                for (let pm in this.content) {
                    this.pencilMarks.push(this.content[pm]);
                }
                this.value = null;
            } else {
                this.pencilMarks = [];
                this.value = this.content;
            }
        },
        isBorderedBottom(row) {
            return row%3==0;
        },
        isBorderedRight(col) {
            return (col+1)%3==0;
        },
        setValue(value) {
            this.pencilMarks = [];
            this.value = value;
        },
        setPencilMarks(pm) {
            let toAdd = []; let toRemove = [];
            pm.forEach((pencilMark, i) => {
                if (this.pencilMarks.indexOf(pencilMark) > -1) {
                    toRemove.push(this.pencilMarks.indexOf(pencilMark));
                } else {
                    toAdd.push(pencilMark);
                }
            });
            // loop from the end so the indexing stays the same for future iterations
            for (var i = toRemove.length-1; i >= 0 ; i--) {
                this.pencilMarks.splice(toRemove[i],1);
            }
            toAdd.forEach((pm, i) => {
                this.pencilMarks.push(pm);
            });


            this.value = null;
        },
        getValue() {
            return this.value;
        },
        getPencilMarks() {
            return this.pencilMarks;
        },
        hasPencilMarks() {
            return this.pencilMarks.length !== 0;
        },
        hasPencilMark(pm) {
            return this.pencilMarks.indexOf(pm) != -1;
        },
        hasValue() {
            return !!this.value;
        },
        toggle() {
            this.$emit('selectCell');
            this.isSelected = this.isSelected ? false : true;
        },
        click() {
            this.toggle();
            let oldCell = null;
            let newCell = null;
            if (this.hasValue()) {
                oldCell = this.value;
            } else if (this.hasPencilMarks()) {
                oldCell = this.pencilMarks.slice();
            }
            if (this.$parent.$parent.pencilMarksMode) {
                this.setPencilMarks(this.$parent.$parent.numbersSelected);
                newCell = this.pencilMarks;
            } else {
                this.setValue(this.$parent.$parent.numbersSelected[0]);
                newCell = this.value;
            }
            if (newCell !== oldCell) {
                this.$parent.$emit('moveMade', {
                    'row':this.row,
                    'col':this.col,
                    'new':newCell,
                    'old':oldCell
                });
            }
        },
    },
    created: function() {
        this.construct();
        this.$root.$on('selectCell', (isSelected) => this.isSelected = false);
    },
    watch: {
        content: {
            handler(newValue, oldValue) {
                this.content = newValue;
                this.construct();
            }
        }
  },
}
</script>
<style lang="scss">
    @import './../../sass/_helpers.scss';
    .sudoku-cell {
        width: rem(60);
        height: rem(60);
        max-width: rem(60);
        max-height: rem(60);
        min-width: rem(60);
        min-height: rem(60);
        border: rem(1) solid black;

        &:hover {
            background:rgba($alpha, 0.1);
        }

        &.selected {
            background:rgba($alpha, 0.3);
        }

        .sudoku-cell-value {
            font-size: rem(30);
        }

        .sudoku-pencil-marks {
            width: 100%;
            line-height: rem(16.5);
            margin:0;
            padding:0;
        }

        &.sudoku-border-right-md {
            border-right: rem(2) solid black;
        }

        &.sudoku-border-bottom-md {
            border-bottom: rem(2) solid black;
        }
    }
</style>
