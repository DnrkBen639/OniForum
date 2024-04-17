<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    public function openNotif(){
        $user = auth()->user();
        $notificaciones = DB::table('notificacions as n')
                ->join('amistads as a', 'n.idAmistad', '=', 'a.id')
                ->join('usuarios as u', 'u.id', '=', 'a.idUsuario2')
                ->select('n.*', 'u.nombre as NombreSolicitud')->where('n.idUsuario', '=', $user->id)
                ->get();

        return response()->json([
            "notificaciones"=>$notificaciones
        ]);
    }
}
