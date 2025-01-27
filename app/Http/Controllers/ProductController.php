<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{
    // Método para obtener la lista de productos con búsqueda y paginación.
    public function index(Request $request)
    {
        // Obtiene todos los productos
        $products = Product::query();

        // Filtra productos según la consulta de búsqueda proporcionada.
        if ($request->searchQuery != '') {
            // % = comodines en SQL, significan "cualquier cosa o nada"
            // busca la palabra clave en cualquier parte del texto
            $products = Product::where('name', 'like', '%' . $request->searchQuery . '%');
        }

        // Ordena los productos por fecha de creación (más recientes primero) y los pagina.
        $products = $products->latest()->paginate(2);

        // Devuelve los productos en formato JSON.
        return response()->json([
            'products' => $products
        ], 200);
    }

    // Método para almacenar un nuevo producto.
    public function store(Request $request)
    {
        // Valida que los campos obligatorios estén presentes.
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        // Nueva instancia de Product 
        $product = new Product();

        // Asigna los valores enviados en el request al modelo Product.
        $product->name = $request->name;
        $product->description = $request->description;

        // Manejo de la imagen subida.
        if ($request->image != "") {
            $strpos = strpos($request->image, ';'); // Encuentra la posición del delimitador ";"
            $sub = substr($request->image, 0, $strpos); // Extrae la información antes del delimitador.
            $ex = explode('/', $sub)[1]; // Obtiene la extensión de la imagen.
            $name = time() . "." . $ex; // Genera un nombre único para la imagen.
            $img = Image::read($request->image)->resize(200, 200); // Redimensiona la imagen a 200x200 px.
            $upload_path = public_path() . "/upload/"; // Define la ruta de subida.
            $img->save($upload_path . $name); // Guarda la imagen en el servidor.
            $product->image = $name; // Almacena el nombre de la imagen en la base de datos.
        } else {
            $product->image = "no-image.png"; // Asigna una imagen predeterminada si no se sube ninguna.
        }

        // Asigna los valores adicionales.
        $product->type = $request->type;
        $product->quantity = $request->quantity;
        $product->price = $request->price;

        // Guarda el producto en la base de datos.
        $product->save();
    }

    // Método para obtener los datos de un producto para edición.
    public function edit($id)
    {
        $product = Product::find($id); // Busca el producto por su ID.

        // Devuelve el producto en formato JSON.
        return response()->json([
            'product' => $product
        ], 200);
    }

    // Método para actualizar un producto existente.
    public function update(Request $request, $id)
    {
        // Valida que los campos obligatorios estén presentes.
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        $product = Product::find($id); // Busca el producto por su ID.

        // Actualiza los valores del producto.
        $product->name = $request->name;
        $product->description = $request->description;

        // Verifica si la imagen ha cambiado.
        if ($request->image != $product->image) {
            $strpos = strpos($request->image, ';');
            $sub = substr($request->image, 0, $strpos);
            $ex = explode('/', $sub)[1];
            $name = time() . "." . $ex;
            $img = Image::make($request->image)->resize(200, 200);
            $upload_path = public_path() . "/upload/";
            $image = $upload_path . $product->image;

            // Elimina la imagen anterior si existe.
            if (file_exists($image)) {
                @unlink($image);
            }

            // Guarda la nueva imagen.
            $img->save($upload_path . $name);
            $product->image = $name;
        } else {
            $product->image = $product->image; // Mantiene la imagen existente.
        }

        // Actualiza los demás valores.
        $product->type = $request->type;
        $product->quantity = $request->quantity;
        $product->price = $request->price;

        // Guarda los cambios en la base de datos.
        $product->save();
    }

    // Método para eliminar un producto.
    public function delete($id)
    {
        $product = Product::findOrFail($id); // Busca el producto por su ID o lanza una excepción si no existe.
        $image_path = public_path() . "/upload/";
        $image = $image_path . $product->image;

        // Elimina la imagen del servidor si existe.
        if (file_exists($image)) {
            @unlink($image);
        }

        // Elimina el producto de la base de datos.
        $product->delete();
    }
}
