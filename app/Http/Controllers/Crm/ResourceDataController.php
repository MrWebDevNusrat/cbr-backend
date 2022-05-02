<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\Cbr;
use App\Models\Crm\Date;
use Illuminate\Http\Request;

class ResourceDataController extends Controller
{
//    public function __construct()
//    {
//        $this->media = new FileController();
//
//        $this->middleware('permission:crm_cbr_index', ['only' => 'index']);
//        $this->middleware('permission:crm_cbr_store', ['only' => 'store', 'show']);
//        $this->middleware('permission:crm_cbr_update', ['only' => 'update', 'show']);
//        $this->middleware('permission:crm_cbr_show', ['only' => 'show']);
//        $this->middleware('permission:crm_cbr_destroy', ['only' => 'destroy']);
//    }

    public function getData(Request $request){

        if (!$date = $request->get('date', null))
            return $this->errorResponse('Date can not be blank', 404);

        $url = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=".$date;

        $xml = simplexml_load_file($url) or die("feed not loading");

        $date = $xml->attributes()->{"Date"};
        $checker = Date::where('date','=',$date)->first();

        if (!$checker){
            $check_date = Date::create([
                'date'=>$date,
                'description'=>$date." date date mapping"
            ]);
            $db_date = $check_date->id;
        }else{
            $check_date = Date::where('date','=',$date)->first();
            $db_date = $check_date->id;
        }

        foreach($xml->children() as $empl) {

            $cbr = Cbr::create([
                'valute_id'=>$empl->attributes()->{"ID"},
                'num_code'=>$empl->NumCode,
                'char_code'=>$empl->CharCode,
                'nominal'=>$empl->Nominal,
                'name'=>$empl->Name,
                'value'=>$empl->Value,
                'date_id'=>$db_date
            ]);

        }

        echo "operation done successfully";
    }

    public function getOneData(Request $request,$value_id){

        if (!$date = $request->get('date', null))
            return $this->errorResponse('Date can not be blank', 404);

        $date_id = Date::max('id');

        $valute = Cbr::select(
                'cbrs.id',
                'cbrs.valute_id',
                'cbrs.num_code',
                'cbrs.char_code',
                'cbrs.nominal',
                'cbrs.value'
            )
            ->where('cbrs.valute_id', $value_id)
            ->where('cbrs.date_id',$date_id)
            ->get();

        return $this->successResponse($valute);
    }

    public function getList(Request $request){
        if (!$date = $request->get('date', null))
            return $this->errorResponse('Date can not be blank', 404);

        $db_date = Date::where('date','=',$date)->first();

        $valute = Cbr::select(
            'cbrs.id',
            'cbrs.valute_id',
            'cbrs.num_code',
            'cbrs.char_code',
            'cbrs.nominal',
            'cbrs.value'
        )
            ->where('cbrs.date_id',$db_date->id)
            ->get();

        return $this->successResponse($valute);
    }
}
