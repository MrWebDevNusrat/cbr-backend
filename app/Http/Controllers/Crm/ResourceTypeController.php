<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resources\FileController;
use App\Models\Crm\Cbr;
use App\Models\Crm\Config;
use App\Models\Crm\Date;
use App\Models\Crm\ResourceType;
use App\Models\Crm\ResourceTypeTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResourceTypeController extends Controller
{
    public function __construct()
    {
        $this->media = new FileController();

        $this->middleware('permission:crm_cbr_index', ['only' => 'index']);
        $this->middleware('permission:crm_cbr_store', ['only' => 'store', 'show']);
        $this->middleware('permission:crm_cbr_update', ['only' => 'update', 'show']);
        $this->middleware('permission:crm_cbr_show', ['only' => 'show']);
        $this->middleware('permission:crm_cbr_destroy', ['only' => 'destroy']);
    }

    public function index(Request $request)
    {
        if (!$language = $request->get('language', null))
            return $this->errorResponse('Language can not be blank', 404);

        $resource_types = ResourceType::join('resource_type_translations', 'resource_type_translations.resource_type_id', '=', 'resource_types.id')
            ->select(
                'resource_types.id',
                'resource_types.category_id',
                'resource_type_translations.name',
                'resource_type_translations.language'
            )
            ->selectRaw("(SELECT  string_agg(language,',')  FROM resource_type_translations WHERE resource_type_translations.resource_type_id = resource_types.id) AS translations")
            ->where([['resource_type_translations.language', $language]])
            ->where(function ($query) use ($request) {
                if ($request->get('title'))
                    $query->where('resource_type_translations.name', 'LIKE', "%{$request->get('name')}%");

            });

        $resource_types = $resource_types->paginate($request->get('limit', Config::key('grid-pagination-limit')));


        return $this->successResponse($resource_types);
    }

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language.*' => 'required|array|exists:languages,code',
            'name.*' => 'required|array|max:255',
            'category_id' => 'required|int|exists:resource_categories,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $resource_type = ResourceType::create([
            'category_id'=>$request->category_id,
            'created_by' => auth()->id(),
        ]);

        foreach ($request->body as $item) {

            $title = $item['name'];
            if ($item['name'] == null) {
                $title = "";
            }


            $tr = ResourceTypeTranslation::create([
                'resource_type_id' => $resource_type->id,
                'language' => $item['language'],
                'name' => $title,
            ]);
        }

        return $this->view($resource_type->id, $request);
    }

    public function update(Request $request, $id)
    {
        if (!$resource_type = ResourceType::where('id', intval($id))->first())
            abort(404);

        $validator = Validator::make($request->all(), [
            'language.*' => 'required|array|exists:languages,code',
            'name.*' => 'required|array|max:255',
            'status' => 'required|integer|in:0,1',
            'category_id' => 'required|int|exists:resource_categories,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $resource_type->update([
            'updated_by' => auth()->id(),
            'status' => $request->status,
            'category_id' =>$request->category_id
        ]);

        foreach ($request->body as $item) {

            $title = $item['name'];
            if ($item['name'] == null) {
                $title = "";
            }

            if (ResourceTypeTranslation::where('resource_type_id', $id)->where('language', $item['language'])->first()) {
                ResourceTypeTranslation::where('resource_type_id', $id)->where('language', $item['language'])
                    ->update([
                        'name' => $title,
                    ]);
            } else {
                ResourceTypeTranslation::create([
                    'resource_type_id' => $resource_type->id,
                    'language' => $item['language'],
                    'name' => $title,
                ]);
            }
        }

        return $this->view($resource_type->id, $request);
    }

    public function show(Request $request, $id)
    {
        return $this->view(intval($id), $request);
    }

    public function destroy($id)
    {
        $resource_type = ResourceType::findOrFail(intval($id));

        $resource_type->update(['deleted_by' => auth()->id()]);
        $resource_type->delete();

        return $this->successResponse('Resource type deleted successfully.');
    }

    protected function view($id, $request)
    {
        $resource_type = ResourceType::join('resource_type_translations', 'resource_type_translations.resource_type_id', '=', 'resource_types.id')
            ->select(
                'resource_types.id',
                'resource_types.category_id',
                'resource_types.status',
                'resource_type_translations.name',
                'resource_type_translations.language'
            )
            ->where('resource_types.id', $id)
            ->where(function ($query) use ($request) {
                if ($request->language)
                    $query->where('resource_type_translations.language', '=', $request->language);

            })->get();

        return $this->successResponse($resource_type);
    }

    public function lists(Request $request)
    {
        $resource_types = ResourceType::join('resource_type_translations', 'resource_type_translations.resource_type_id', '=', 'resource_types.id')
            ->select(
                'resource_types.id',
                'resource_types.category_id',
                'resource_type_translations.name',
                'resource_types.status',
                'resource_type_translations.language'
            )
            ->where(function ($query) use ($request) {
                if ($request->language)
                    $query->where('resource_type_translations.language', '=', $request->language);
                if ($request->name)
                    $query->where('resource_type_translations.name', 'LIKE', "%{$request->title}%");

            })->get();
        return $this->successResponse($resource_types);
    }
}
