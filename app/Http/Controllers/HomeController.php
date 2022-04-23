<?php

namespace App\Http\Controllers;

use App\Models\Crm\Cbr;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $valutes = Cbr::join('dates', 'dates.id', '=', 'cbrs.date_id')
        ->select(
            'cbrs.id',
            'cbrs.valute_id',
            'cbrs.num_code',
            'cbrs.char_code',
            'cbrs.nominal',
            'cbrs.value',
            'dates.date as date'
        )->get();
        return view('welcome',['valutes'=>$valutes]);
    }
}
