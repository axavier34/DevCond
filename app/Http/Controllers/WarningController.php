<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Supporte\Facades\Storege;
use Illuminate\Supporte\Facades\Validator;

use App\Models\Warning;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class WarningController extends Controller
{
    public function getMyWarnings(Request $request){
        $array = ['error' => ''];

        $property = $request->input('property');

        if($property){

            $user = auth()->user();

            $unit = Unit::where('id',$property)
            ->where('id_owner', $user['id'])
            ->count();

            if($unit>0){
                $warnings = Warning::where('id_unit',$property)
                ->orderBy('datecreated', 'DESC')
                ->orderBy('id','DESC')
                ->get();

                foreach($warnings as $warningKey =>$warningValues){
                    $warnings[$warningKey]['datecreated'] = date('d/m/Y', strtotime($warningValues['datecreated']));
                    $photosList = [];
                    $photos = explode(',',$warningValues['photos']);
                    foreach($photos as $photo){
                        if(!empty($photo)){
                            $photosList[] = asset('storage/'.$photo);
                        }
                    }
                    $warnings[$warningKey]['photos'] = $photosList;
                }

                $array['list'] = $warnings;
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

    public function addWarningFile(Request $request){
        $array = ['error' => ''];
        
        $validator = FacadesValidator::make($request->all() ,[
            'photo' => 'required|file|mimes:jpg,png'
        ]);

        if(!$validator->fails()){
            $file = $request->file('photo')->store('public');
            $array['photo'] = asset(Storage::url($file));
        }
        else{
            $array['erro'] =$validator->errors()->first();
            return $array;
        }
        return $array;
    }

    public function setWarning(Request $request){
        $array = ['error' => ''];
        $validator = FacadesValidator::make($request->all() ,[
            'title' => 'required',
            'property' => 'required'
        ]);

        if(!$validator->fails()){
            $title = $request->input('title');
            $property =$request->input('property');
            $list = $request->input('list');

            $newWarn = new Warning();
            $newWarn->id_unit = $property;
            $newWarn->title =  $title;
            $newWarn->status = 'IN_REVIEW';
            $newWarn->datecreated = date('Y-m-d');
            if($list && is_array($list)){
                $photos = [];
                foreach($list as $listItem){
                    $url = explode('/',$listItem);
                    $photos[] = end($url);
                }
                $newWarn->photos = implode(',', $photos);
            }
            else{
                $newWarn->photos = '';
            }
            $newWarn->save();
        }
        else{
            $array['erro'] =$validator->errors()->first();
            return $array;
        }
        return $array;
    }
}
