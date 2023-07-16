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
use App\Laravue\Models\KhachHang;
use App\Laravue\Models\ChiTietCombo;
use App\Laravue\Models\ChiTietHoaDon;
use App\Laravue\Models\HoaDon;
use Carbon\Carbon;
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
class ChiTietHoaDonController extends BaseController
{
    
    public function index(Request $request)
    {
        $searchParams = $request->all();
        // dd($searchParams);
        $query = [];
        $query = ChiTietHoaDon::select(['chi_tiet_hoa_don.*','hoa_don.ten_khach_hang','hoa_don.sdt'])->join('hoa_don','hoa_don.id','=','chi_tiet_hoa_don.ma_hoa_don');
        if(isset($searchParams['is_combo']) && !empty($searchParams['is_combo'])) {
            $query->where('chi_tiet_hoa_don.is_combo','1');
        }
        if(isset($searchParams['sdt']) && !empty($searchParams['sdt'])) {
            $query->where('hoa_don.sdt',$searchParams['sdt']);
        }
        if(isset($searchParams['ma_dich_vu']) && !empty($searchParams['ma_dich_vu'])) {
            $query->where('chi_tiet_hoa_don.ma_dich_vu',$searchParams['ma_dich_vu']);
        }
        $query->orderBy('created_at','desc');
        $data = $query->get()->toArray();
        foreach($data as $key => $v) {
            $data[$key]['chi_tiet'] = ChiTietCombo::where('ma_chi_tiet_hd', $v['id'])->orderBy('created_at')->get()->toArray();
        }

        return response()->json(['data' => $data], 200);
    }

    public function storeCombo(Request $request) {
        $id  = $request->input('id');
        $chi_tiet = ChiTietHoaDon::where('id',$id)->first();
        if($chi_tiet->so_luong_con_lai > 0) {
            $chi_tiet->so_luong_con_lai = $chi_tiet->so_luong_con_lai - 1;
            $chi_tiet->save();
            ChiTietCombo::insert(['ma_chi_tiet_hd' => $id, 'created_at' => Carbon::now()]);
            return response()->json(['message' => "Cập nhật thành công!"], 200);
        }
        return response()->json(['message' => "Số lượng trong combo của khách đã hết!"], 500);
    }

    public function store(Request $request) {
        $id = $request->get("id","");
        $ten = $request->get("ten_phong","");
        $gia = $request->get("gia",0);
        $type = $request->get("type",1);
        
        // dd($ten,$gia,$request->all());

        try {

            if(empty($id)) {
                // $query = Phong::insert(['ten_phong'=>$ten,'gia'=>$gia]);
                $model = new KhachHang;
                $model->ten_phong = $ten;
                $model->gia = $gia;
                $model->type = $type;
                $model->save();
                return response()->json(['message' => "Thành công !","success" => true,"id"=> $model->id]);
            }
            else {
                $query = KhachHang::where('id',$id)->update(['ten_phong' => $ten,'gia'=>$gia]);
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
                $query = KhachHang::where('id',$id)->delete();
            }
            return response()->json(['message' => "Thành công !","success" => true]);
        }catch (\Exception $ex) {
            return response()->json(['message' => $ex,"success" => false]);
        }
        

    }

    
}
