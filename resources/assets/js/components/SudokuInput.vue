<template>
    <form action="" method="" @submit.prevent="onSubmit">
        <div class="row">
            <input class="input input-text col col-8" name="grid" type="text" placeholder="81-digit grid" v-model="encoding"/>
            <button class="button alpha col col-2 m-l-30" type="submit">Solve</button>
        </div>
    </form>
</template>
<script>
export default {
    name: "",
    data: () => ({
        encoding:"",
    }),
    methods: {
        onSubmit() {
            if (this.encoding.length === 81) {
                axios.post(
                    '/api/solver',
                    { 
                        grid: this.encoding,
                        returnFormat: 'row'
                    }
                ).then(response => {
                    let encoding = response.data.data.solved_grid;
                    this.$root.$emit('write-grid', encoding);
                }).catch(error => {
                    let errors = error.response.data.errors;
                    for (let i in errors) {
                        toastr.error(errors[i]);
                    }
                });
            } else {
                toastr.error('Given grid must be 81 characters long');
            }
        },
    }
}
</script>
<style lang="scss">
</style>
