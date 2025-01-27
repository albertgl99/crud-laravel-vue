<script setup>
import { onMounted, reactive, ref } from "vue"
import { useRouter, useRoute } from "vue-router"

// Inicializa el router para navegación y la ruta actual para obtener parámetros.
const router = useRouter()
const route = useRoute()

// Almacena los errores de validación de la API.
let errors = ref([])

// Bandera para determinar si se está creando o editando un producto.
const editMode = ref(false)

// Ejecuta la función cuando el componente se monta.
onMounted(() => {
    if (route.name === 'products.edit') { // Si la ruta es "editar producto", cambia a modo edición.
        editMode.value = true
        getProduct() // Obtiene los datos del producto a editar.
    }
})

// Obtiene los datos de un producto específico para edición.
const getProduct = async () => {
    let response = await axios.get(`/api/products/${route.params.id}/edit`)
        .then((response) => {
            // Rellena el formulario con los datos obtenidos.
            form.name = response.data.product.name
            form.description = response.data.product.description
            form.image = response.data.product.image
            form.type = response.data.product.type
            form.quantity = response.data.product.quantity
            form.price = response.data.product.price
        })
        .catch((error) => {
            console.error("Error al obtener el producto:", error) // Manejo de errores.
        })
}

// Define el formulario reactivo.
const form = reactive({
    name: "",
    description: "",
    image: "",
    type: "",
    quantity: "",
    price: "",
})

// Devuelve la imagen a mostrar (por defecto o personalizada).
const getImage = () => {
    let image = "/upload/no-image.png" // Imagen predeterminada.
    if (form.image) {
        if (form.image.indexOf("base64") != -1) {
            image = form.image // Usa la imagen en base64 si está disponible.
        } else {
            image = "/upload/" + form.image // Ruta a la imagen en el servidor.
        }
    }
    return image
}

// Maneja el cambio de archivo para subir una nueva imagen.
const handleFileChange = (e) => {
    let file = e.target.files[0] // Obtiene el archivo seleccionado.
    let reader = new FileReader()
    reader.onloadend = () => {
        form.image = reader.result // Convierte el archivo en base64 y lo asigna al formulario.
    }
    reader.readAsDataURL(file) // Lee el archivo como un Data URL.
}

// Decide si crear o actualizar un producto según el modo.
const handleSave = (values, actions) => {
    if (editMode.value) {
        updateProduct(values, actions) // Actualiza el producto existente.
    } else {
        createProduct(values, actions) // Crea un nuevo producto.
    }
}

// Crea un nuevo producto llamando a la API.
const createProduct = (values, actions) => {
    axios.post('/api/products', form)
        .then((response) => {
            router.push('/') // Redirige al usuario al inicio después de guardar.
            toast.fire({ icon: "success", title: "Product Added Successfully" }) // Notificación de éxito.
        })
        .catch((error) => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors // Muestra errores de validación.
            }
        })
}

// Actualiza un producto existente llamando a la API.
const updateProduct = (values, actions) => {
    axios.put(`/api/products/${route.params.id}`, form)
        .then((response) => {
            router.push('/') // Redirige al usuario al inicio después de guardar.
            toast.fire({ icon: "success", title: "Product Updated Successfully" }) // Notificación de éxito.
        })
        .catch((error) => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors // Muestra errores de validación.
            }
        })
}
</script>

<template>
    <div class="products__create ">
    
    <div class="products__create__titlebar dflex justify-content-between align-items-center">
        <div class="products__create__titlebar--item">
            
            <h1 class="my-1">
            <span v-if="editMode">Edit</span>    
            <span v-else>Create</span>
            Product</h1>
        </div>
        <div class="products__create__titlebar--item">
            
            <button @click="handleSave" class="btn btn-secondary ml-1" >
                Save
            </button>
        </div>
    </div>

    <div class="products__create__cardWrapper mt-2">
        <div class="products__create__main">
            <div class="products__create__main--addInfo card py-2 px-2 bg-white">
                <p class="mb-1">Name</p>
                <input type="text" class="input" v-model="form.name" >

                <small style="color:red" v-if="errors.name">{{ errors.name }}</small>

                <p class="my-1">Description (optional)</p>
                <textarea cols="10" rows="5" class="textarea" v-model="form.description"></textarea>
                
                <small style="color:red" v-if="errors.description">{{ errors.description }}</small>

                <div class="products__create__main--media--images mt-2">
                   <ul class="products__create__main--media--images--list list-unstyled">
                       <li class="products__create__main--media--images--item">
                           <div class="products__create__main--media--images--item--imgWrapper">
                               <img :src="getImage()" class="products__create__main--media--images--item--img" alt="" />  
                           </div>
                       </li>
                       <!-- upload image small -->
                       <li class="products__create__main--media--images--item">
                           <form class="products__create__main--media--images--item--form">
                               <label class="products__create__main--media--images--item--form--label" for="myfile">Add Image</label>
                               <input class="products__create__main--media--images--item--form--input" type="file" id="myfile" @change="handleFileChange">
                           </form>
                       </li>
                   </ul>
               </div>
           
            </div>

        </div>
        <div class="products__create__sidebar">
            <!-- Product Organization -->
            <div class="card py-2 px-2 bg-white">
                
                <!-- Product unit -->
                <div class="my-3">
                    <p>Product type</p>
                    <input type="text" class="input" v-model="form.type" >
                </div>
                <hr>

                <!-- Product invrntory -->
                <div class="my-3">
                    <p>Inventory</p>
                    <input type="text" class="input" v-model="form.quantity" >
                </div>
                <hr>

                <!-- Product Price -->
                <div class="my-3">
                    <p>Price</p>
                    <input type="text" class="input" v-model="form.price" >
                </div>
            </div>

        </div>
    </div>
    <!-- Footer Bar -->
    <div class="dflex justify-content-between align-items-center my-3">
        <p ></p>
        <button @click="handleSave" class="btn btn-secondary" >Save</button>
    </div>

</div>

</template>