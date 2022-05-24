<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Supporte\Facades\Storege;

use App\Models\Billet;
use App\Models\Unit;

class BilletController extends Controller
{
    public function getAll(Request $request){
        $array = ['error' => '', 'list'=> []];

        $property = $request->input('property');

        if($property){

            $user = auth()->user();

            $unit = Unit::where('id',$property)
            ->where('id_owner', $user['id'])
            ->count();

            if($unit>0){
                $billet = Billet::where('id_unit',$property)->get();

                foreach($billet as $billetKey =>$billetValues){
                    $walls[$billetKey]['fileurl'] = asset('storage/'.$billetValues['fileurl']);
                }

                $array['list'] = $billet;
            }
            
            else{
                $array['error'] = 'Esta unidade não é Sua';
            }
        }
        else{
            $array['error'] = 'A propriedade é necssária';
        }
        return $array;
    }
}
