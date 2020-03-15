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
                  <tr v-for="row in 9" :key="row">
                      <sudoku-cell
                           v-for="(cell, col) in getCells(9*(row-1), 9*row)"
                           :key="row+''+col"
                           :content="cell" :col="col" :row="row"
                           @moveMade="setLastMove">
                       </sudoku-cell>
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
                cells:[],
                lastMove:{}
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
                        let encoding = response.data.data.solved_grid;
                        for (let i = 0; i < 81; i++) {
                            this.$set(this.cells, i, encoding[i]);
                        }
                    }
                );
            },
            undo() {
                if (Object.keys(this.lastMove).length !== 0) {
                    this.$set(this.cells, (this.lastMove['row']-1)*9+this.lastMove['col'], this.lastMove['old']);
                }
            },
            getCells(begin=0,end=81) {
                return this.cells.slice(begin,end);
            },
            getCell(row,col) {
                return this.cells[row*9+col]
            },
            setLastMove(lm) {
                this.lastMove = lm;
            }
        },
        created: function() {
            for (let i = 0; i < 81; i++) {
                this.cells[i] = null;
            }
            this.$root.$on('undo', () => this.undo());
        },
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
    }
</style>
