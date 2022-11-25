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
use App\Laravue\Models\HoaDon;
use App\Laravue\Models\Phong;
use App\Laravue\Models\DichVu;
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
class HoaDonController extends BaseController
{
    
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $query = [];
        if(isset($searchParams['id'])) {
            $query = HoaDon::find($searchParams['id']);
        }
        else{

            $query = HoaDon::select(['hoa_don.*','phong.ten_phong','nguoi_thue.ten'])->leftJoin('phong','hoa_don.ma_phong','phong.id')
            ->leftJoin('nguoi_thue','hoa_don.ma_nguoi_thue','nguoi_thue.id')->get()->toArray();
            // dd($query);
        }

        return response()->json(['data' => $query], 200);
    }

    public function store(Request $request) {
        $id = $request->get("id","");
        $ma_phong = $request->get("ma_phong","0");
        $ma_nguoi_thue = $request->get("ma_hop_dong",0);
        $so_dien = $request->get("so_dien",0);
        $so_nuoc = $request->get("so_nuoc",0);
        $month = $request->get("month",date('m'));
        $year = $request->get("year",date('Y'));
        $tien_phat_sinh = $request->get("tien_phat_sinh",0);
        $da_thanh_toan = $request->get("da_thanh_toan",0);

        // dd($ten,$gia,$request->all());

        try {

            if(empty($id)) {
                $so_dien_pre = 0;
                $so_nuoc_pre = 0;
                $gia_phong = 0;
                $tien_dien = 0;
                $tien_nuoc = 0;
                $tien_dich_vu = 0;
                $tong_tien = 0;
                $so_nguoi = 1;
                $hoa_don_pre = HoaDon::where(['ma_phong' => $ma_phong, 'month' => $month == 12 ? 1 : $month - 1, 'year' => $month == 12 ? $year - 1 : $year ])->first();
                $delete = HoaDon::where(['ma_phong' => $ma_phong, 'month' => $month , 'year' =>$year ])->delete();
                if($hoa_don_pre){
                    $so_dien_pre = $hoa_don_pre->so_dien;
                    $so_nuoc_pre = $hoa_don_pre->so_nuoc;
                }
                $phong = Phong::find($ma_phong);
                if($phong) $gia_phong = $phong->gia;

                $hop_dong = HopDong::where(['ma_phong' => $ma_phong, 'trang_thai' => 1])->first();
                if($hop_dong) {
                    $ma_nguoi_thue = $hop_dong->id;
                    $so_nguoi = $hop_dong->so_nguoi_thue;
                }
                else {
                    return response()->json(['message' => "Phòng chưa có người thuê !","success" => false]);
                }

                $dich_vu = DichVu::all();
                
                foreach($dich_vu as $dv) {
                    if($dv["trang_thai"] == 0) {
                        $tien_dien = ($so_dien - $so_dien_pre) * $dv["gia"];
                    }
                    if($dv["trang_thai"] == 1) $tien_nuoc = ($so_nuoc - $so_nuoc_pre) * $dv["gia"];
                    if($dv["trang_thai"] == 2) $tien_dich_vu += $dv["gia"];
                    if($dv["trang_thai"] == 3) $tien_dich_vu += $dv["gia"] * $so_nguoi;
                }
                $tong_tien = $tien_dien + $tien_nuoc + $tien_dich_vu + $gia_phong + $tien_phat_sinh;
                $model = new HoaDon;
                $model->ma_phong = $ma_phong;
                $model->ma_nguoi_thue = $ma_nguoi_thue;
                $model->so_dien = $so_dien;
                $model->so_nuoc = $so_nuoc;
                $model->so_dien_pre = $so_dien_pre;
                $model->so_nuoc_pre = $so_nuoc_pre;
                $model->tien_phat_sinh = $tien_phat_sinh;
                $model->month = $month;
                $model->year = $year;
                $model->tong_tien = $tong_tien;
                $model->da_thanh_toan = $da_thanh_toan;
                $model->ngay_dong = date("Y-m-d");
                $model->save();
                return response()->json(['message' => "Thành công !","success" => true,"id"=> $model->id]);
            }
            else {
                $model = HoaDon::find($id);
                // $model->ten = $ten;
                // $model->nam_sinh = $nam_sinh;
                // $model->ngay_vao = $ngay_vao;
                // $model->ma_phong = $ma_phong;
                // $model->que_quan = $que_quan;
                // $model->da_coc = $da_coc;
                // $model->trang_thai = $trang_thai;
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
                $query = HoaDon::where('id',$id)->delete();
            }
            return response()->json(['message' => "Thành công !","success" => true]);
        }catch (\Exception $ex) {
            return response()->json(['message' => $ex,"success" => false]);
        }
        

    }

    
}
