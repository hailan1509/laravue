<?php
/**
 * File UserController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Laravue\JsonResponse;
use App\Laravue\Models\Permission;
use App\Laravue\Models\Role;
use App\Laravue\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Api
 */
class PhongController extends BaseController
{
    
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $query = [];
        if(isset($searchParams['id'])) {
            $query = Phong::find($searchParams['id']);
        }
        else{

            $query = Phong::all()->toArray();
            // dd($query);
        }

        return response()->json(['data' => $query], 200);
    }

    public function store(Request $request) {
        $id = $request->get("id","");
        $ten = $request->get("ten_phong","");
        $gia = $request->get("gia",0);
        
        // dd($ten,$gia,$request->all());

        try {

            if(empty($id)) {
                // $query = Phong::insert(['ten_phong'=>$ten,'gia'=>$gia]);
                $model = new Phong;
                $model->ten_phong = $ten;
                $model->gia = $gia;
                $model->save();
                return response()->json(['message' => "Thành công !","success" => true,"id"=> $model->id]);
            }
            else {
                $query = Phong::where('id',$id)->update(['ten_phong' => $ten,'gia'=>$gia]);
            }

            return response()->json(['message' => "Thành công !","success" => true]);
        }
        catch (\Exception $e){
            return response()->json(['message' => $e,"success" => false]);
        }
    }

    public function delete(Request $request) {
        $id = $request->get("id","");
        try{
            if(!empty($id)) {
                $query = Phong::where('id',$id)->delete();
            }
            return response()->json(['message' => "Thành công !","success" => true]);
        }catch (\Exception $ex) {
            return response()->json(['message' => $ex,"success" => false]);
        }
        

    }

    
}
