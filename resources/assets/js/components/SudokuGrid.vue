<template>
    <div>
        <table class="sudoku-grid">
              <tr v-for="row in 9" :key="row">
                  <sudoku-cell
                       v-for="(cell, col) in getCells(9*(row-1), 9*row)"
                       :key="row+''+col"
                       :content="cell" :col="col" :row="row"
                       @selectCell="emitSelection">
                   </sudoku-cell>
              </tr>
        </table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                cells:[],
                lastMoves:[],
            }
        },
        methods: {
            undo() {
                if (Object.keys(this.$parent.lastMoves).length !== 0) {
                    let lastMove = this.$parent.lastMoves.pop();
                    this.$set(this.cells, (lastMove['row']-1)*9+lastMove['col'], lastMove['old']);
                }
            },
            getCells(begin=0,end=81) {
                return this.cells.slice(begin,end);
            },
            getCell(row,col) {
                return this.cells[row*9+col]
            },
            /**
             * This should be called when a 'select' emit is received from the
             * cell.  What we do is intercept the emit and reemit-it to $root
             * but only if we are not in select-mode.
             */
            emitSelection() {
                if (!this.$parent.selectionMode) {
                    this.$root.$emit('selectCell');
                }
            },
            writeValues(encoding = []) {
                for (let i = 0; i < 81; i++) {
                    if (i in encoding) {
                        this.$set(this.cells, i, encoding[i]);
                    } else {
                        this.$set(this.cells, i, null);
                    }
                }
            }
        },
        created: function() {
            this.writeValues(); // so and empty grid is shown on load
            this.$root.$on('undo', () => this.undo());
            this.$root.$on('write-grid', (e) => this.writeValues(e));
        },
    }
</script>
<style lang="scss">
    @import './../../sass/_helpers.scss';
    .sudoku-grid {
        height:rem(570);
        width: rem(570);
        border: rem(2) solid black;
        text-align: center;
        border-collapse: collapse;
        font-weight: 300;
    }
</style>
