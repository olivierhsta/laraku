<template>
    <td class="sudoku-cell"
        :class="{'sudoku-border-right-md': isBorderedRight(col),'sudoku-border-bottom-md': isBorderedBottom(row)} "
        @click="click()">
        <table v-if="hasPencilMarks()"
               class="sudoku-pencil-marks">
            <tr v-for="i in 3" :key="i">
                <td v-for="j in 3" class="p-0" :key="j">
                    <small v-if="hasPencilMark((i-1)*3+j)">
                        {{ (i-1)*3+j }}
                    </small>
                    <small style="visibility:hidden;" v-else>
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
        numbersSelected: [],
        pencilMarksMode:false,
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
            this.pencilMarks = [...this.pencilMarks, ...pm];;
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
        select() {
            this.isSelected = true;
        },
        unselect() {
            this.isSelected = false;
        },
        click() {
            let oldCell = null;
            let newCell = null;
            if (this.value !== null) {
                oldCell = this.value;
            } else if (this.pencilMarks !== []) {
                oldCell = this.pencilMarks;
            }
            if (this.pencilMarksMode) {
                this.setPencilMarks(this.numbersSelected);
                newCell = this.pencilMarks;
            } else {
                this.setValue(this.numbersSelected[0]);
                newCell = this.value;
            }
            this.$emit('moveMade', {
                'row':this.row,
                'col':this.col,
                'new':newCell,
                'old':oldCell
            });
        },
    },
    created: function() {
        this.construct();
        this.$root.$on('number-selected', (ns) => this.numbersSelected = ns);
        this.$root.$on('pencilmarks-mode-toggle', (pmm) => this.pencilMarksMode = pmm);
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

        &.sudoku-border-right-md {
            border-right: rem(2) solid black;
        }

        &.sudoku-border-bottom-md {
            border-bottom: rem(2) solid black;
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
    }
</style>
