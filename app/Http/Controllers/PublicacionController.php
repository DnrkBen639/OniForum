<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\usuario;
use App\Models\publicacion;
use App\Models\content;
use Illuminate\Support\Facades\DB;

class PublicacionController extends Controller
{
    public function manejarImagenes($file){
        $nameFile = uniqid();
        $extensionFile = '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/',$nameFile.$extensionFile);
        $storageRoute = storage_path('app/public/'.$nameFile.$extensionFile);
        $publicRoute = public_path('content/'.$nameFile.$extensionFile);
        File::move($storageRoute,$publicRoute);
        Storage::delete($storageRoute);
        return $nameFile.$extensionFile;
    }

    public function Publicar(Request $request){
        //dd($request);
        $request->validate([
            'titulo' => 'required|string',
            'contenido.*' => 'required|file|mimes:jpg,png,jpeg,gif,svg,webp',
        ]);

        $user = auth()->user();

        $publicacion = new publicacion();
        $publicacion->idUsuario = $user->id;
        $publicacion->titulo = $request->titulo;
        $publicacion->save();

        

        if($request->has('contenido')){
            
            foreach($request->file('contenido') as $file){
                $ruta = $this->manejarImagenes($file);
                $contenido = new content();
                $contenido->idPublicacion = $publicacion->id;
                $contenido->dirContent = $ruta;
                $contenido->save();
            }
        }else{
            return response()->json(['status'=>'vale gorro']);
        }
        
        return response()->json(['success'=>true,'data'=>$publicacion]);

    }

    public function readRequest(Request $request){

        return response()->json(['data'=>$request->contenido]);
    }

    public function openPost(Request $request){

        $contenido=content::where("idPublicacion", "=" ,$request->idPublicacion)->get();

        $comentarios = DB::table('comentarios as c')
        ->join('usuarios as u', 'u.id', '=', 'c.idUsuario')
        ->select('c.contenido', 'u.nombre')
        ->where('c.idPublicacion', '=', $request->idPublicacion)
        ->get();

        $likes = DB::table('usuarios as u')
        ->join('likes as l', 'l.idUsuario', '=', 'u.id')
        ->select('u.nombre')
        ->where('l.idPublicacion', '=', $request->idPublicacion)
        ->get();

        return response()->json(['imagenes'=>$contenido,
                                 'comentarios'=>$comentarios,
                                 'likes'=>$likes]);

    }
}
