<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Favorie;
use App\Models\Group;
use App\Models\Livre;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;



class Users extends Controller
{
   
    public function login(Request $request)
    {
        $user = DB::table('Users')->where('email',$request->input('email'))->where('password',$request->input('password'))->first();
        $response=[
            'user'=>$user
        ];
        return $response;
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom'=>'required',
            'prenom'=>'required',
            'email' => ['required','email'],
            'password' => 'required',
            'confirmation'=>'required|same:password'
        ]);
        if($validator->fails()){
            $response=[
                'success'=>'register failed',
                'message'=>$validator->errors()
            ];
            return $response;
        }
        else{
            $user=new User();
            $user->nom=$request->input('nom');
            $user->prenom=$request->input('prenom');
            $user->email=$request->input('email');
            $user->password=$request->input('password');
            $user->role = 0;
            $user->save();
            return 'new user added';
        }
    }

    public function supprimerCompte(Request $request)
    {
       $user=User::find($request->id);
       $user->delete();
       return 'user deleted';
    }

    public function modifierCompte(Request $request,$id)
    {
       $user=User::find($id);
       $user->nom=$request->input('nom');
       $user->prenom=$request->input('prenom');
       $user->email=$request->input('email');
       $user->password=$request->input('password');
       $user->save();
       return ['etat'=>'updated'];
    }


    public function ajouterFavorie(Request $request)
    {
        $count=Favorie::select('id_livre','id_user')
        ->where('id_livre','=', $request->id_livre)
        ->where('id_user','=', $request->id_user)
        ->count();
        if($count==0){
            $favorie=new Favorie();
            $favorie->id_livre=$request->input('id_livre');
            $favorie->id_user=$request->input('id_user');
            $favorie->save();
            return "added";
        }
        else{
           return "deja";
        }
    }

    public function getFavories($id)
    {
        return livre::select('livres.id','livres.nom AS nom_livre','livres.image','livres.pdf', 'livres.created_at','categories.nom AS nom_cat','favories.id as id_favorie')
        ->join('categories', 'livres.id_cat','=','categories.id')
        ->join('favories', 'favories.id_livre','=','livres.id')
        ->where('livres.isArchived','=',0)
        ->where('favories.id_user','=',$id)
        ->get();
    }

    public function supprimerFavorie($id)
    {
       $favorie=Favorie::find($id);
       $favorie->delete();
       return 'deleted';
    }
    
    public function rejoindreGroup(Request $request)
    {
        $count=Membre::select('id_group','id_user')
        ->where('id_group','=', $request->id_group)
        ->where('id_user','=', $request->id_user)
        ->count();
        if($count==0){
            $membre=new Membre();
            $membre->id_group=$request->id_group;
            $membre->id_user=$request->id_user;
            $membre->save();
            return 'added';
        }
        else{
           return "deja";
        }
    }


    public function supprimerMongroup(Group $group)
    {
        $group->delete();
        return 'deleted';
    }

    public function mesGroupes($id)
    {
        $groupes=Group::select('id','nom','description','created_at')
        ->where('id_user','=',$id)
        ->get();
        return $groupes;
    }

    public function getLivrebyDate($date)
    {
        return livre::select('livres.id','livres.nom AS nom_livre','livres.image','livres.pdf', 'livres.created_at','categories.nom AS nom_cat')
        ->join('categories', 'livres.id_cat','=','categories.id')
        ->where('livres.isArchived','=',0)
        ->where('livres.created_at','=',$date)
        ->get();

    }

    public function getLivrebyCat($cat)
    {
        return livre::select('livres.id','livres.nom AS nom_livre','livres.image','livres.pdf', 'livres.created_at','categories.nom AS nom_cat')
        ->join('categories', 'livres.id_cat','=','categories.id')
        ->where('livres.isArchived','=',0)
        ->where('livres.id_cat','=',$cat)
        ->get();
    }

    public function getLivrebyNom($nom)
    {
        return livre::select('livres.id','livres.nom AS nom_livre','livres.image','livres.pdf', 'livres.created_at','categories.nom AS nom_cat')
        ->join('categories', 'livres.id_cat','=','categories.id')
        ->where('livres.isArchived','=',0)
        ->where('livres.nom','like',$nom)
        ->get();
    }
    
}

