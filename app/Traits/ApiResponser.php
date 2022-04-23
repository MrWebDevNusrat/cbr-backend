<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{

    protected function successResponse($result, $message = null, $code = 200)
    {
        return response()->json([
            'result' => $result
        ], $code);
    }

    protected function errorResponse($message = null, $code)
    {
        return response()->json([
            'message' => $message,
        ], $code);
    }

}
