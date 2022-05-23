<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Supporte\Facades\Storege;

use App\Models\Doc;

class DocController extends Controller
{
    public function getAll(){
        $array = ['error' => '', 'list'=> []];

        $docs = Doc::all();

        foreach($docs as $docKey =>$docValues){
            $walls[$docKey]['fileurl'] = asset('storage/'.$docValues['fileurl']);
        }

        $array['list'] = $docs;
        
        return $array;
    }
}
