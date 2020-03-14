<template>
    <div>
        <div class="m-l-75">
            <form action="" method="" @submit.prevent="onSubmit">
                <div class="row">
                    <input class="input input-text col col-8" name="grid" type="text" placeholder="81-digit grid" v-model="encoding"/>
                    <button class="button col col-2 m-l-30" type="submit">Solve</button>
                </div>
            </form>
            <table class="sudoku-grid m-t-20">
                  <tr v-for="i in 9" :key="i">
                      <td class="sudoku-cell"
                          :class="{'sudoku-border-right-md': isBorderedCell(j+1),'sudoku-border-bottom-md': isBorderedRow(i)} "
                          @click="click(cell)"
                           v-for="(cell, j) in grid.getCells(9*(i-1), 9*i)"
                           :key="j">
                          <table v-if="cell.hasPencilMarks()"
                                 class="sudoku-pencil-marks">
                              <tr v-for="i in 3" :key="i">
                                  <td v-for="j in 3" class="p-0" :key="j">
                                      <small v-if="cell.hasPencilMark((i-1)*3+j)">
                                          {{ (i-1)*3+j }}
                                      </small>
                                      <small style="visibility:hidden;" v-else>
                                          0
                                      </small>
                                  </td>
                              </tr>
                          </table>
                          <span v-else-if="cell.hasValue()" class="sudoku-cell-value" v-text="cell.value"></span>
                      </td>
                  </tr>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                encoding : "",
                grid: new Grid(),
                numberSelected: [],
                pencilMarksMode:false,
            }
        },
        methods: {
            onSubmit() {
                axios.post(
                    '/api/solver',
                    { grid: this.encoding,
                      returnFormat: 'row'
                    }
                ).then(
                    response => {
                        this.grid = new Grid(response.data.data.solved_grid);
                    }
                );
            },
            cellClicked(cell) {
                return cell.isSelected;
            },
            click(cell) {
                if (this.pencilMarksMode) {
                    cell.setPencilMarks(this.numberSelected);
                } else {
                    cell.setValue(this.numberSelected[0]);
                }
            },
            isBorderedRow(i) {
                return i%3==0;
            },
            isBorderedCell(i) {
                return i%3==0;
            }
        },
        created: function() {
            this.$root.$on('number-selected', (ns) => this.numberSelected = ns);
            this.$root.$on('pencilmarks-mode-toggle', (pmm) => this.pencilMarksMode = pmm);
        },
    }

    class Grid {
        constructor(encoding = []) {
            this.cells = new Array();
            if (encoding) {
                for (let i = 0; i < 81; i++) {
                    this.cells[i] = new Cell(encoding[i]);
                }
            }
        }

        getCells(begin=0,end=81) {
            return this.cells.slice(begin,end);
        }
    }

    class Cell {
        constructor(content) {
            this.isSelected = false;
            if (Array.isArray(content)) {
                this.pencilMarks = [];
                for (let pm in content) {
                    this.pencilMarks.push(content[pm]);
                }
                this.value = null;
            } else {
                this.pencilMarks = [];
                this.value = content;
            }
        }

        setValue(value) {
            this.pencilMarks = [];
            this.value = value;
        }

        setPencilMarks(pm) {
            this.pencilMarks = pm;
            this.value = null;
        }

        hasPencilMarks() {
            return this.pencilMarks.length !== 0;
        }

        hasPencilMark(pm) {
            return this.pencilMarks.indexOf(pm) != -1;
        }

        hasValue() {
            return !!this.value;
        }

        select() {
            this.isSelected = true;
        }

        unselect() {
            this.isSelected = false;
        }
    }
</script>
<style lang="scss">
    @import './../../sass/_helpers.scss';
    .sudoku-grid {
        height:rem(540);
        width: rem(540);
        border: rem(2) solid black;
        text-align: center;
        border-collapse: collapse;
        font-weight: 300;

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
    }
</style>
