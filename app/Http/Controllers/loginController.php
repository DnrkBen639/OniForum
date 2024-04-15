<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\usuario;
use App\Models\amistad;
use App\Models\publicacion;
use App\Models\notificacion;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class loginController extends Controller
{
    
    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = new usuario();
        $user->nombre = $request->name;
        $user->email = $request->email;
        $user->passw = Hash::make($request->password);

        $user->save();

        return response()->json([
            "status" => 1,
            "msg" => "Registro de usuario exitoso"
        ]);
    }

    public function login(Request $request){
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $user = usuario::where("email", "=", $request->email)->first();


        if( isset($user->id)){
            if(Hash::check($request->password, $user->passw)){
                //Crear token
                $token = $user->createToken("auth_token")->plainTextToken;

                $IdAmigos = amistad::select('idusuario2')->where('idusuario1', '=', $user->id)->where('aceptado', '=', 1)->get();
        
                $publicacions = publicacion::leftJoin('likes AS l', 'publicacions.id', '=', 'l.idPublicacion')
                ->join('usuarios AS u', 'publicacions.idUsuario', '=', 'u.id')
                ->select('u.nombre', 'publicacions.titulo', 'publicacions.descripcion', 'publicacions.created_at', 'publicacions.updated_at', DB::raw('COUNT(l.id) AS num_likes'))
                ->groupBy('u.nombre', 'publicacions.titulo', 'publicacions.descripcion', 'publicacions.created_at', 'publicacions.updated_at')
                ->whereIn('publicacions.idusuario', $IdAmigos)->get();
                
                $numNotificaciones = notificacion::where('idUsuario', '=', $user->id)
                ->count();


                $notificaciones = DB::table('notificacions as n')
                ->join('amistads as a', 'n.idAmistad', '=', 'a.id')
                ->join('usuarios as u', 'u.id', '=', 'a.idUsuario2')
                ->select('n.*', 'u.nombre as NombreSolicitud')
                ->get();


                return response()->json([
                    "status" => 1,
                    "msg" => "Login exitoso",
                    "access_token" => $token,
                    "UserInfo"=>$user,
                    "NumNotificaciones"=>$numNotificaciones,
                    "Notificaciones"=>$notificaciones,
                    "Publicacions"=>$publicacions
                ]);
            }else{
                return response()->json([
                    "status" => 0,
                    "msg" => "La contrasena es incorrecta"
                ]);
            }
        }else{
            return response()->json([
                "status"=> 0,
                "msg" => "El usuario no está registrado"
            ]);
        }


    }

    

}
