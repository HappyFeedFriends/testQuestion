<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/checkedPattern',function(Request $request){

    $mask = $request->get('mask');
    $str = $request->get('value');
    // validation data
    if (!$str || !$mask) {
        return [
            'error' => 'not valid data',
        ];
    }

    $code = explode("\n",$str);
    $count = 0;
    $patternsSearch = [
        'Z', // '[-_@]'
        'N', // '[0-9]'
        'A', // '[A-Z]'
        'a', // '[a-z]'
        'X'  // '[A-Z0-9]'
    ];

    $patternReplace = [
        '[-_@]',
        '[0-9]',
        '[A-Z]',
        '[a-z]',
        '[A-Z0-9]',
    ];

    // validation mask
    $idMask = DB::table('TypesEquip')->select('id')->where('mask',$mask)->get()->first();
    if (!preg_match('/^[ZNAaX]+$/',$mask) || !$idMask || !$idMask->id){
        return [
            'error' => 'not valid mask',
        ];
    }

    $result = [];
    $databasePush = [];
    foreach ($code as $key => $value) {
        $value = preg_replace('/\s+/','',$value);
        $patternUpdate = '/^' . str_replace($patternsSearch,$patternReplace,$mask,$count) . '$/';
        $isValid = !!preg_match($patternUpdate,$value);
        $isHaveInDatabase = !!DB::table('equip')->select()->where('serialNumber',$value)->get()->first();
        array_push($result,[
            'value' => $value,
            'regExpPattern' => $patternUpdate,
            'isValid' => $isValid,
            'mask' => $mask,
            'isHaveInDatabase' => $isHaveInDatabase,
        ]);

        if ($isValid && !$isHaveInDatabase){
            array_push($databasePush,[
                'serialNumber' => $value,
                'equipID' => $idMask->id,
            ]);
        }
    }

    if (count($databasePush) > 0){
        DB::table('equip')->insert($databasePush);
    }

    return $result;
});
