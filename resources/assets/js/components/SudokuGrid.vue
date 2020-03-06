<template>
    <div class="row">
        <div class="col col-6">
            <form action="" method="" @submit.prevent="onSubmit">
                <div class="row">
                    <input class="input input-text col col-10" name="grid" type="text" placeholder="81-digit grid" v-model="encoding"/>
                    <button class="button col col-2 m-l-25" type="submit">Solve</button>
                </div>
            </form>
            <table :class="'sudoku-grid' + hiddenGridClass">
                  <tr v-for="i in 9" :class="{'border-bottom-lg': isBorderedRow(i)}">
                      <td class="sudoku-cell" :class="{'border-right-lg': isBorderedCell(j+1)}"
                          @click="click(cell)"
                           v-for="(cell, j) in grid.getCells(9*(i-1), 9*i)">
                          <table v-if="cell.hasPencilMarks()"
                                 class="sudoku-pencil-marks">
                              <tr v-for="i in 3">
                                  <td v-for="j in 3" class="padding-none">
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
    </div>
</template>

<script>
    export default {
        data() {
            return {
                encoding : "",
                grid: new Grid(),
                hiddenGridClass: 'd-none'
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
                        this.hiddenGridClass = '';
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
        constructor(encoding = null) {
            this.cells = new Array();
            if (encoding) {
                for (let i = 0; i < encoding.length; i++) {
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
<style media="screen">
    .sudoku-grid {
        height:36em;
        width: 36em;
        border: 2px solid black;
        text-align: center;
        border-collapse: collapse;
        font-weight: 300;
    }

    .sudoku-cell {
        margin:0;;
        padding:0;
        vertical-align: middle;
        border: 1px solid black;
    }

    .sudoku-pencil-marks {
        width: 100%;
        line-height: 1.2;
        margin:0;
        padding:0;
    }

    .sudoku-cell-value {
        font-size: xx-large;
    }

    .padding-0 {
        padding:0 !important;
    }

    .border-right-lg {
        border-right: 2px solid black !important;
    }

    .border-bottom-lg {
        border-bottom: 2px solid black !important;
    }
</style>
