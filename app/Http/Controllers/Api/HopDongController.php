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
use App\Laravue\Models\HopDong;
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
class HopDongController extends BaseController
{
    
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $query = [];
        if(isset($searchParams['id'])) {
            $query = HopDong::find($searchParams['id']);
        }
        else{

            $query = HopDong::select(['nguoi_thue.*','phong.ten_phong'])->leftJoin('phong','nguoi_thue.ma_phong','phong.id')->get()->toArray();
            // dd($query);
        }

        return response()->json(['data' => $query], 200);
    }

    public function store(Request $request) {
        $id = $request->get("id","");
        $ten = $request->get("ten","");
        $nam_sinh = $request->get("nam_sinh",0);
        $ngay_vao = $request->get("ngay_vao","");
        $trang_thai = $request->get("trang_thai",1);
        $ma_phong = $request->get("ma_phong","");
        $que_quan = $request->get("que_quan","");
        $da_coc = $request->get("da_coc",0);
        $so_nguoi_thue = $request->get("so_nguoi_thue",0);
        // dd($ten,$gia,$request->all());

        try {

            if(empty($id)) {
                // $query = HopDong::insert(['ten_HopDong'=>$ten,'gia'=>$gia]);
                if($trang_thai != 1)
                    $un_active = HopDong::where('ma_phong',$ma_phong)->update(['trang_thai' => 0]);
                $model = new HopDong;
                $model->ten = $ten;
                $model->nam_sinh = $nam_sinh;
                $model->ngay_vao = $ngay_vao;
                $model->ma_phong = $ma_phong;
                $model->que_quan = $que_quan;
                $model->da_coc = $da_coc;
                $model->trang_thai = $trang_thai;
                $model->so_nguoi_thue = $so_nguoi_thue;
                $model->save();

                
                return response()->json(['message' => "Thành công !","success" => true,"id"=> $model->id]);
            }
            else {
                $model = HopDong::find($id);
                $model->ten = $ten;
                $model->nam_sinh = $nam_sinh;
                $model->ngay_vao = $ngay_vao;
                $model->ma_phong = $ma_phong;
                $model->que_quan = $que_quan;
                $model->da_coc = $da_coc;
                $model->trang_thai = $trang_thai;
                $model->so_nguoi_thue = $so_nguoi_thue;
                $model->save();
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
                $query = HopDong::where('id',$id)->delete();
            }
            return response()->json(['message' => "Thành công !","success" => true]);
        }catch (\Exception $ex) {
            return response()->json(['message' => $ex,"success" => false]);
        }
        

    }

    
}
