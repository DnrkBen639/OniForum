<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\publicacion;
use App\Models\notificacion;
use App\Models\usuario;



class UsuarioController extends Controller
{
    public function manejarImagenes($file){
        $nameFile = uniqid();
        $extensionFile = '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/',$nameFile.$extensionFile);
        $storageRoute = storage_path('app/public/'.$nameFile.$extensionFile);
        $publicRoute = public_path('perfiles/'.$nameFile.$extensionFile);
        File::move($storageRoute,$publicRoute);
        Storage::delete($storageRoute);
        return $nameFile.$extensionFile;
    }

    public function userProfile(){

        $user = auth()->user();

        $publicaciones = publicacion::where('idUsuario', '=',  $user->id)->get();
        $notificaciones = notificacion::where('idUsuario', '=',  $user->id)->get();

        return response()->json([
            "status"=> 0,
            "msg"=>"Acerca del perfil de usuario",
            "data" => auth()->user(),
            "publicaciones"=>$publicaciones,
            "notificaciones"=>$notificaciones
        ]);

    }

    public function PhotoUpdate(Request $request){
        
        $request->validate([
            'foto' => 'required|image',
        ]);

        $user = auth()->user();

        try {
            
            $user->dirFoto = $this->manejarImagenes($request->foto);
        } catch (\Exception $e) {
            
            return response()->
            json(['error' => 'No se pudo actualizar la foto de perfil',
                                     'errors' => $e], 500);
        }
        
        $user->save();
        return response()->json(['success' => 'Foto de perfil actualizada correctamente'], 200);
    }

    public function logout(){
        auth()->user()->tokens() -> delete();
        return response()->json([
            "status"=> 1,
            "msg"=>"Logout  exitoso"
        ]);

    }

    
}
