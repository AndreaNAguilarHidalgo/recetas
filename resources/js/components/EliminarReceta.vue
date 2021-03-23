<template>
    <input type="submit" class="btn btn-danger d-block mb-2 w-100" 
        value="Eliminar ×" @click="eliminarReceta">
</template>

<script>
export default 
{
    props: ['recetaId'],

    methods: 
    {
        eliminarReceta()
        {
            this.$swal
            ({
                    title: '¿Deseas eliminar esta receta?',
                    text: "Una vez eliminada, no se puede recuperar",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
            }).then((result) => 
            {
                if(result.value)
                {
                    const params = {
                        id: this.recetaId
                    }
                    // Enviar peticion a servidor
                    
                    axios.post(`recetas/${this.recetaId}`, {params, _method: 'delete'})

                    .then( respuesta => {
                         this.$swal
                        ({
                            title: 'Receta Eliminada',
                            text: 'Se eliminó la receta',
                            icon: 'success'
                        });

                        // Eliminar receta del DOM
                        this.$el.parentNode.parentNode.parentNode.removeChild(this.$el.parentNode.parentNode);
                    })
                    .catch(error => {
                        console.log(error)
                    })
                }
            })
        }
    }
}
</script>