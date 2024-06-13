<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\ApiFormatter;
use App\Models\InBoundStuff;
use App\Models\Stuff;
use App\Models\StuffStock;

class InBoundStuff extends Model
{
    use SoftDeletes; //digunakan hanya untuk table yang menggunakan fitur soft deletes
    protected $fillable = ["stuff_id", 
    "total", "date", "proff_file"];

    public function stuff()
    {
        return $this->belongsTo(Stuff::class);
    }

    public function store(){
        try {
            $this->validate($request, [
                'stuff_id' => 'required',
                'total' => 'required',
                'date' => 'required',
                'proff_file' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            if($request->hasFile('proff_file')){
                $prof = $request->file('proff_file');
                $destinationPath = 'proof/';
                $proofname = date('YmdHis') . "." . $proof->getClientOriginalExtension();
                $proof->move($destinationPath, $proofName);
            }

            $createStock = InboundStuff::create([
                'stuff_id' => $request->$stuff_id,
                'total' => $request->total,
                'date' => $request->date,
                'proof_file' => $request->proff_file,
            ]);

            if($createStock) {
                $getStuff = Stuff::where('id', $request->stuff_id)->first();
                $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();

                if(!$getStuffStock){
                    $updateStock = StuffStock::create([
                        'stuff_id' => $request->stuff_id,
                        'total_available' => $request->total,
                        'total_defac' => 0,
                    ]);
                }else{
                    $updateStock = $getStuffStock::update([
                        'stuff_id' => $request->stuff_id,
                        'total_available' => $getStuffStock['total_available'] + $request->total,
                        'total_defac' =>  $getStuffStock['total_defac'],
                    ]);
                }
                
                if(!updateStock){
                    $getStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
                    $stuff = [
                        'stuff' => $getStuff,
                        'InBoundstuff' => $createStock,
                        'stuffStock' => $getStuff,
                    ];

                    return ApiFormatter::sendresponse(200, true, 'successfully create a inbound stuff data', $stuff);
                }else{
                    return ApiFormatter::sendResponse(400, false, 'failed to update a stuff stock data');
                }
                
            }else{
                return ApiFormatter::sendResponse(400, false, 'failed to crate a inbound stuff data');
            }
        } catch (\Exception $e){
            return ApiFormatter::sendResponse(400, true, $e->getMessage());
        }
    }
}
