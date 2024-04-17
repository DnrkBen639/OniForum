<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\amistad;
use App\Models\notificacion;
use App\Models\usuario;

class AmistadController extends Controller
{
    public function SendFriendReq(Request $request){
        $user = auth()->user();
        $amistad = new amistad();
        $amistad->idUsuario1 = $request->idUsuario;
        $amistad->idUsuario2 = $user->id;
        $amistad->aceptado = 0;
        $amistad->save();

        $objective = usuario::where('id', '=', $request->idUsuario)->first();

        $notificacion = new notificacion;
        $notificacion->titulo = 'Solicitud de amistad';
        $notificacion->descripcion = "Ha recibido una solicitud de amistad de ". $user->nombre .".";
        $notificacion->idUsuario = $request->idUsuario;
        $notificacion->idAmistad = $amistad->id;
        $notificacion->save();

        return response()->json([
            'status'=>'Se envió una solicitud de amistad a '. $objective->nombre
        ]);

    }

    public function SearchUser(Request $request){
        $users = usuario::where('nombre', '=', $request->busqueda)->orWhere('email', '=', $request->busqueda)->get();

        return response()->json([
            'usuarios'=>$users
        ]);
    }

    public function AcceptFriend(Request $request){
        //Aceptar la peticion de amistad
        $amistad = amistad::where('id', '=', $request->idAmistad)->first();
        $amistad->aceptado = 1;
        $amistad->save();

        $notification = notificacion::where('idAmistad', '=', $request->idAmistad);
        $notification->delete();

        return response()->json([
            'status'=>'La solicitud fue aceptada.'
        ]);
    }

    public function DenyFriend(Request $request){
        //Denegar la peticion de amistad
        $amistad = amistad::where('id', '=', $request->idAmistad);
        $notification = notificacion::where('idAmistad', '=', $request->idAmistad);
        $notification->delete();
        $amistad->delete();
        return response()->json([
            'status'=>'La solicitud fue rechazada.'
        ]);
    }


}
