<template>
    <div class="row">
        <div class="col col-5 m-l-75">
            <form action="" method="" @submit.prevent="onSubmit">
                <div class="row">
                    <input class="input input-text col col-8" name="grid" type="text" placeholder="81-digit grid" v-model="encoding"/>
                    <button class="button col col-2 m-l-10" type="submit">Solve</button>
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
                                  </td>
                              </tr>
                          </table>
                          <span v-if="cell.hasValue()" class="sudoku-cell-value" v-text="cell.value"></span>
                      </td>
                  </tr>
            </table>
        </div>
        <div>
            <table class="control-grid m-t-55">
                <tr class="" v-for="i in 9" :key="i">
                    <td class="control-cell">{{i}}</td>
                </tr>
            </table>
        </div>
        <div>
            <table class="control-grid m-t-55">
                <tr><td class="control-cell" alt="Pencil Mark" title="Pencil Mark"><svg-vue icon="edit" class="control-icon"></svg-vue></td></tr>
                <tr><td class="control-cell" alt="Undo" title="Undo"><svg-vue icon="undo" class="control-icon"></svg-vue></td></tr>
                <tr><td class="control-cell" alt="Select" title="Select"><svg-vue icon="touch" class="control-icon"></svg-vue></td></tr>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                encoding : "",
                grid: new Grid()
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
                for (let i = 0; i < this.grid.getCells().length; i++) {
                    let otherCell = this.grid.getCells()[i];
                    otherCell.unselect();
                }
                cell.select();
            },
            isBorderedRow(i) {
                return i%3==0;
            },
            isBorderedCell(i) {
                return i%3==0;
            }
        }
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
        height:rem(560);
        width: rem(560);
        border: rem(2) solid black;
        text-align: center;
        border-collapse: collapse;
        font-weight: 300;

        .sudoku-cell {
            width: rem(60);
            height: rem(60);
            margin:0;
            padding:0;
            vertical-align: middle;
            border: rem(1) solid black;

            &.sudoku-border-right-md {
                border-right: rem(2) solid black;
            }

            &.sudoku-border-bottom-md {
                border-bottom: rem(2) solid black;
            }

            .sudoku-cell-value {
                font-size: xx-large;
            }

            .sudoku-pencil-marks {
                width: 100%;
                line-height: 1.2;
                margin:0;
                padding:0;
            }
        }
    }

    .control-grid {
        border-collapse:collapse;
        height: rem(560);

        .control-cell {
            width: rem(60);
            margin:0;
            text-align: center;
            font-weight: 700;
            vertical-align: middle;
            border: rem(1) solid $light-gray;

            .control-icon {
                width: 50%;
                padding: rem(1) 0;
            }
        }
    }
</style>
