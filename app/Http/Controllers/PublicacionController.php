<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\usuario;
use App\Models\publicacion;
use App\Models\content;
use App\Models\shared;
use App\Models\comentario;
use App\Models\like;
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
                $contenido->extension = $file->getClientOriginalExtension();
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

        if($contenido->isEmpty()){
            $contenido=DB::table('shareds as s')
            ->join('contents as c', 'c.id', '=', 's.idContent')
            ->select('c.*')
            ->where('s.idPublicacion', '=', $request->idPublicacion)->get();
        }

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

    public function LikePost(Request $request){

        $user = auth()->user();
        $existingLike = Like::where('idUsuario', $user->id)
                            ->where('idPublicacion', $request->idPublicacion)
                            ->first();

        if ($existingLike) {
            $existingLike->delete();
            return response()->json([
                'status' => 'ok',
                'action'=>'dislike'
            ]);
        } else {
            
            $like = new Like();
            $like->idUsuario = $user->id;
            $like->idPublicacion = $request->idPublicacion;
            $like->save();

            return response()->json([
                'status' => 'ok',
                'action'=>'like'
            ]);
        }

    }

    public function SharePost(Request $request){
        $user = auth()->user();
        $publicacion = publicacion::where('id', '=', $request->idPublicacion)->first();
        $Repost = new publicacion();
        $Repost->idUsuario = $user->id;
        $Repost->titulo =$publicacion->titulo;
        $Repost->descripcion = $publicacion->descripcion;
        $Repost->save();

        $contents = content::where('idPublicacion', '=', $request->idPublicacion)->get();

        foreach($contents as $content){
            $shared = new shared();
            $shared->idPublicacion =  $Repost->id;
            $shared->idContent = $content->id;
            $shared->save();
        }


        return response()->json([
            'status' => 'ok',
            'mensaje'=>'Se ha compartido la Publicación correctamente.',
            'Repost'=>$Repost,
            'Contenidos'=>$contents
        ]);
        
    }

    public function ComentPost(Request $request){
        $user = auth()->user();
        $comentario = new comentario();
        $comentario->contenido = $request->comentario;
        $comentario->idUsuario = $user->id;
        $comentario->idPublicacion = $request->idPublicacion;
        $comentario->save();

        return response()->json([
            'status'=>'Se publicó el comentario',
            'comentario'=>$comentario
        ]);
    }
}
