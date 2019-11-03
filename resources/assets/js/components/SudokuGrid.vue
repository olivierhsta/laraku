<template>
    <div>
        <form action="" method="" @submit.prevent="onSubmit">
            <div class="row">
                <input name="grid" type="text" class="form-control col-10" placeholder="81-digit grid" v-model="encoding"/>
                <button type="submit" class="btn btn-primary col-2 form-control">Solve</button>
            </div>
        </form>
        <table :class="'table table-bordered sudoku-grid' + hiddenGridClass">
              <tr scope="row" v-for="i in 9">
                  <td class="sudoku-cell" v-for="cell in grid.getCells(9*(i-1), 9*i)">
                      <table v-if="Array.isArray(cell)" class="table table-borderless sudoku-pencil-marks">
                          <td v-for="pencil_mark in cell" class="padding-0">
                              <small class="{{ $mark == 0 ? "invisible" : "" }}">{{ pencil_mark }}</small>
                          </td>
                      </table>
                      <span class="sudoku-cell-value" v-text="cell.value"></span>
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
        constructor(value) {
            this.pencilMarks = {};
            this.value = value;
        }
    }
</script>
