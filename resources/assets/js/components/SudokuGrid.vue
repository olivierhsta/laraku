<template>
    <div>
        <form action="" method="" @submit.prevent="onSubmit">
            <div class="row">
                <input name="grid" type="text" class="form-control col-10" placeholder="81-digit grid" v-model="encoding"/>
                <button type="submit" class="btn btn-primary col-2 form-control">Solve</button>
            </div>
        </form>
        <table :class="'table table-bordered sudoku-grid' + hiddenGridClass">
              <tr scope="row" v-for="i in 9" :class="{'border-bottom-lg': isBorderedRow(i) }">
                  <td class="sudoku-cell" :class="{'border-right-lg': isBorderedCell(j+1) }"
                       v-for="(cell, j) in grid.getCells(9*(i-1), 9*i)">
                      <table v-if="cell.hasPencilMarks()"
                             class="table table-borderless sudoku-pencil-marks">
                          <tr v-for="i in 3">
                              <td v-for="j in 3" class="padding-0">
                                  <small v-if="cell.hasPencilMark((i-1)*3+j)">
                                      {{ (i-1)*3+j }}
                                  </small>
                                  <small v-else class="invisible">0</small>
                              </td>
                          </tr>
                      </table>
                      <span v-if="cell.hasValue()" class="sudoku-cell-value" v-text="cell.value"></span>
                  </td>
              </tr>
        </table>
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
                      returnFormat: 'row' }
                ).then(
                    response => {
                        this.grid = new Grid(response.data.data.solved_grid);
                        this.hiddenGridClass = '';
                    }
                );
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
    }
</script>
