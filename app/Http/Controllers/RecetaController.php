<?php

namespace App\Http\Controllers;

use App\Receta;
use App\User;
use App\CategoriaReceta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\Promise\all;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class RecetaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except'=> ['show', 'search']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$usuario = auth()->user();
        //$recetas = auth()->user()->recetas;

        $usuario = auth()->user();

        // Receta con paginación
        $recetas = Receta::where('user_id', $usuario->id)->paginate(10);

        return view('recetas.index')->with('recetas', $recetas)->with('usuario', $usuario);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Obtener categorias sin modelo
        //$categorias = DB::table('categoria_recetas')->get()->pluck('nombre', 'id');

        //Categorias con modelo
        $categorias = CategoriaReceta::all(['id','nombre']);

        return view('recetas.create')->with('categorias', $categorias);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validacion
        $data = $request->validate([
            'titulo' => 'required|min:6',
            'ingredientes' => 'required',
            'preparacion' => 'required',
            'categoria' => 'required',
            'imagen' => 'required|image'
            
        ]);

        // Obtener ruta de la imagen
        $ruta_imagen = $request['imagen']->store('upload-recetas', 'public');

        // Resize img
        $img = Image::make(public_path("storage/{$ruta_imagen}"))->resize(350, 150);
        $img->save();

        // Cargar datos en DB
        //DB::table('recetas')->insert([
        //    'titulo' => $data['titulo'],
        //    'ingredientes' => $data['ingredientes'],
        //    'preparacion'=> $data['preparacion'],
        //    'imagen' => $ruta_imagen,
        //    'user_id' => Auth::user()->id,
        //    'categoria_id' => $data['categoria']
        //]);

        // Almacenar datos con model
        auth()->user()->recetas()->create([
            'titulo' => $data['titulo'],
            'preparacion'=> $data['preparacion'],
            'ingredientes' => $data['ingredientes'],
            'imagen' => $ruta_imagen,
            'categoria_id' => $data['categoria']
        ]);

        // Redireccionar
        return redirect() -> action('RecetaController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function show(Receta $receta)
    {
        // Obtener si el usuario actual le gusta la receta y esta autenticado
        $like = (auth()->user()) ? auth()->user()->meGusta->contains($receta->id) : false;

        // Pasar cantidad de likes en recetas
        $likes = $receta->likes->count();
        //
        return view('recetas.show', compact('receta', 'like', 'likes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function edit(Receta $receta)
    {
        //
        $this->authorize('view', $receta);

        $categorias = CategoriaReceta::all(['id','nombre']);

        return view('recetas.edit', compact('categorias', 'receta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receta $receta)
    {
        // Revisar el policy
        $this->authorize('update', $receta);

        //Validacion
        $data = $request->validate([
            'titulo' => 'required|min:6',
            'ingredientes' => 'required',
            'preparacion' => 'required',
            'categoria' => 'required',
            
        ]);

        //Asignar valores
        $receta->titulo = $data['titulo'];
        $receta->ingredientes = $data['ingredientes'];
        $receta->preparacion = $data['preparacion'];
        $receta->categoria_id = $data['categoria'];

        if(request('imagen'))
        {
            // Obtener ruta de la imagen
            $ruta_imagen = $request['imagen']->store('upload-recetas', 'public');

            // Resize img
            $img = Image::make(public_path("storage/{$ruta_imagen}"))->resize(350, 150);
            $img->save();

            // Se asigna al objeto
            $receta->imagen = $ruta_imagen;
      
        }

        $receta->save();

        //Redireccionar
        return redirect()->action('RecetaController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receta $receta)
    {
        // Revisar el policy
        $this->authorize('delete', $receta);

        // Eliminar Receta
        $receta->delete();

        //Redireccionar
        return redirect()->action('RecetaController@index');
    }

    public function search(Request $request) 
    {
        // $busqueda = $request['buscar'];
        $busqueda = $request->get('buscar');

        $recetas = Receta::where('titulo', 'like', '%' . $busqueda . '%')->paginate(10);
        $recetas->appends(['buscar' => $busqueda]);

        return view('busquedas.show', compact('recetas', 'busqueda'));
    }
}
