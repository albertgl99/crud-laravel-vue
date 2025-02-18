<script setup>
import { onMounted, ref, watch } from "vue"
import { useRouter } from "vue-router"

// Inicializa el router para la navegación entre páginas.
const router = useRouter()

// Variables reactivas para almacenar productos, enlaces de paginación y la consulta de búsqueda.
let products = ref([]) // Lista de productos.
let links = ref([]) // Enlaces de paginación.
let searchQuery = ref('') // Consulta de búsqueda actual.

// Ejecuta la función getProducts al montar el componente.
onMounted(async () => {
    getProducts() // Llama a la API para obtener productos al cargar la página.
})

// Observa cambios en searchQuery y llama a getProducts para actualizar la lista.
watch(searchQuery, () => {
    getProducts() // Actualiza la lista de productos cuando cambia la consulta.
})

// Redirige al usuario a la página para crear un nuevo producto.
const newProduct = () => {
    router.push('/products/create') // Navega a la ruta para agregar un producto.
}

// Construye la URL de la imagen del producto.
const ourImage = (img) => {
    return "/upload/" + img // Devuelve la ruta completa de la imagen.
}

// Obtiene la lista de productos de la API.
const getProducts = async () => {
    let response = await axios.get('/api/products?&searchQuery=' + searchQuery.value)
        .then((response) => {
            products.value = response.data.products.data // Almacena los productos obtenidos.
            links.value = response.data.products.links // Almacena los enlaces de paginación.
        })
        .catch((error) => {
            console.error(error) // Manejo de errores en la consola.
        })
}

// Cambia de página utilizando los enlaces de paginación.
const changePage = (link) => {
    if (!link.url || link.active) {
        return // Si no hay URL o ya está activa, no hace nada.
    }

    axios.get(link.url)
        .then((response) => {
            products.value = response.data.products.data // Actualiza los productos.
            links.value = response.data.products.links // Actualiza los enlaces.
        })
        .catch((error) => {
            console.error(error) // Manejo de errores en la consola.
        })
}

// Redirige al usuario a la página de edición de un producto específico.
const onEdit = (id) => {
    router.push(`/products/${id}/edit`) // Navega a la ruta de edición con el ID del producto.
}

// Elimina un producto después de confirmar la acción.
const deleteProduct = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            // Llama a la API para eliminar el producto.
            axios.delete(`/api/products/${id}`)
                .then(() => {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        icon: "success"
                    })
                    getProducts() // Actualiza la lista después de la eliminación.
                })
                .catch((error) => {
                    console.error(error) // Manejo de errores en la consola.
                })
        }
    })
}
</script>

<template>
    <div class="container">
      
      <div class="products__list table  my-3">
                
          <div class="customers__titlebar dflex justify-content-between align-items-center">
              <div class="customers__titlebar--item">
                  <h1 class="my-1">Products</h1>
              </div>
              <div class="customers__titlebar--item">
                  <button @click="newProduct" class="btn btn-secondary my-1" >
                      Add Product
                  </button>
              </div>
          </div>

          <div class="max-w-full">
                <form class="flex items-center max-w mx-auto">   
                    <label for="simple-search" class="sr-only">Search</label>
                    <div class="relative w-full">
                        <input type="text" id="simple-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-4 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search branch name..." v-model="searchQuery" />
                    </div>
                </form>
          </div>
  
          <div class="table--heading mt-2 products__list__heading " style="padding-top: 20px;background:#FFF">
              <!-- <p class="table--heading--col1">&#32;</p> -->
              <p class="table--heading--col1">image</p>
              <p class="table--heading--col2">
                  Product
              </p>
              <p class="table--heading--col4">Type</p>
              <p class="table--heading--col6">
                  Price
              </p>
              <!-- <p class="table--heading--col5">&#32;</p> -->
              <p class="table--heading--col5">actions</p>
          </div>
  
          <!-- product 1 -->
          <div class="table--items products__list__item"  v-for="product in products" :key="product.id">
              <div class="products__list__item--imgWrapper">
                  <img :src="ourImage(product.image)" class="products__list__item--img"  style="height: 40px;">
              </div>
              <a href="# " class="table--items--col2">
                  {{ product.name}}
              </a>
              <p class="table--items--col2">
                {{ product.type}}
              </p>
              <p class="table--items--col3">
                {{ product.quantity}}
              </p>     
              <div>     
                  <button @click="onEdit(product.id)">
                    <svg class="w-6 h-6 text-gray-800 dark:text-black items-center hover:text-green-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                    </svg>

                  </button>
                  <button class="pl-4" @click="deleteProduct(product.id)">
                    <svg class="w-6 h-6 text-gray-800 dark:text-black hover:text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                    </svg>

                  </button>
              </div>
          </div>


<div class="flex border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
  <div class="flex flex-1 justify-between sm:hidden">
    <a href="#" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
    <a href="#" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
  </div>
  <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
    
        <div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
                
                <!-- Current: "z-10 bg-indigo-600 text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600", Default: "text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0" -->
                <a 
                href="#" 
                class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                v-for="(link, index) in links"
                :key="index"
                v-html="link.label"
                :class="{ active: link.active, disabled: !link.url }"
                @click="changePage(link)"
                ></a>
                
            </nav>
            </div>
        </div>
        </div>
  
        </div>
    </div>
</template>
