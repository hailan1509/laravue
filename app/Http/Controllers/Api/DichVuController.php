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
use App\Laravue\Models\DichVu;
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
class DichVuController extends BaseController
{
    
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $query = [];
        if(isset($searchParams['id'])) {
            $query = DichVu::find($searchParams['id']);
        }
        else{
            if(isset($searchParams['search'])) {
                // dd($searchParams['search']);
                $query = DichVu::where('ten_dich_vu','like', '%'.$searchParams['search'].'%')->get()->toArray();
            }
            else
                $query = DichVu::all()->toArray();
            // dd($query);
        }

        return response()->json(['data' => $query], 200);
    }

    public function store(Request $request) {
        $id = $request->get("id","");
        $ten = $request->get("ten_dich_vu","");
        $gia = $request->get("gia",0);
        $gia_khuyen_mai = $request->get("gia_khuyen_mai",0);
        $ten_khuyen_mai = $request->get("ten_khuyen_mai",'');
        $gia = $request->get("gia",0);
        $trang_thai = $request->get("trang_thai","1");
        // dd($ten,$gia,$request->all());

        try {

            if(empty($id)) {
                // $query = DichVu::insert(['ten_DichVu'=>$ten,'gia'=>$gia]);
                $model = new DichVu;
                $model->ten_dich_vu = $ten;
                $model->gia = $gia;
                $model->trang_thai = $trang_thai;
                $model->ten_khuyen_mai = $ten_khuyen_mai;
                $model->gia_khuyen_mai = $gia_khuyen_mai;
                $model->save();
                return response()->json(['message' => "Thành công !","success" => true,"id"=> $model->id]);
            }
            else {
                $query = DichVu::where('id',$id)->update(['ten_dich_vu' => $ten,'gia'=>$gia, 'trang_thai' => $trang_thai, 'ten_khuyen_mai' => $ten_khuyen_mai,'gia_khuyen_mai' => $gia_khuyen_mai,]);
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
                $query = DichVu::where('id',$id)->delete();
            }
            return response()->json(['message' => "Thành công !","success" => true]);
        }catch (\Exception $ex) {
            return response()->json(['message' => $ex,"success" => false]);
        }
        

    }

    
}
