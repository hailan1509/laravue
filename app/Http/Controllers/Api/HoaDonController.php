<?php
/**
 * File UserController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */

namespace App\Http\Controllers\Api;

use App\Laravue\Models\HoaDon;
use App\Laravue\Models\KhachHang;
use App\Laravue\Models\ChiTietHoaDon;
use App\Laravue\Models\DichVu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Carbon\Carbon;

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
            return response()->json(['data' => $query], 200);
        }
        else{
            $date = $searchParams['date'];
            $month = $searchParams['month'];
            $phone = $searchParams['phone'];
            $query = HoaDon::select(['hoa_don.*']);
            if($month) {
                $arr_date = explode('/',$date);
                $query->whereMonth('ngay', $arr_date[1])->whereYear('ngay', $arr_date[0]);
            }
            else {
                $query->whereDate('ngay', $date);
            }
            if(!empty($phone)) {
                $query->where('sdt', $phone);
            }
            $data = $query->whereNull('deleted_at')->get();
            $total = 0;
            foreach($data as $key => $v) {
                $data[$key]['chi_tiet'] = ChiTietHoaDon::where('ma_hoa_don', $v['id'])->get();
                $data[$key]['so_luong'] = count($data[$key]['chi_tiet']);
                $total += (int)$v['tong_tien'];
            }  
            return response()->json(['data' => $data, 'total' => $total], 200);
        }

    }

    public function store(Request $request) {

        $name = $request->get('name', '');
        $phone = $request->get('phone', '');
        $total = $request->get('total', '0');
        $delivery = $request->get('delivery', 'false');
        $data = $request->get('data', []);
        if(empty($data) || empty($name) || empty($phone)) {
            return response()->json(['message' => 'Tham số không đẩy đủ',"success" => false]);
        }
        $pass = false;
        DB::transaction(function() use($name, $phone, $data, $delivery, $total, &$pass) {
            try {
                $kh = KhachHang::where('sdt',$phone)->get();
                if(count($kh) == 0) {
                    KhachHang::create(['ten'=> $name, 'sdt' => $phone]);
                }
                // HoaDon::create(['ten_khach_hang'=> $name, 'sdt' => $phone , 'tong_tien' => $total, 'chuyen_khoan' => $delivery == 'false' ? '0' : '1']);
                $hoa_don_new = new HoaDon();
                $hoa_don_new->ten_khach_hang = $name;
                $hoa_don_new->sdt = $phone;
                $hoa_don_new->tong_tien = $total;
                $hoa_don_new->chuyen_khoan = $delivery == 'false' ? '0' : '1';
                $hoa_don_new->ngay = Carbon::now();
                $hoa_don_new->save();
                $id_new = $hoa_don_new->id;            
                foreach($data as $v) {
                    $values = ['ma_hoa_don' => $id_new,'ma_dich_vu' => $v['id'], 'ten_dich_vu' => $v['ten_dich_vu_km'], 'gia' => $v['gia'], 'gia_khuyen_mai' => $v['gia_khuyen_mai'], 'soluong' => $v['soluong'], 'is_combo' => $v['is_combo'] ? 1 : 0, 'so_luong_combo' => $v['so_luong_combo'] * $v['soluong'], 'so_luong_con_lai' => $v['so_luong_combo'] * $v['soluong']];
                    ChiTietHoaDon::create($values);
                }
                $pass = true;
                DB::commit();
            }
            catch (\Exception $e){
                // dd($e);
                DB::rollback();
                return response()->json(['message' => $e,"success" => false]);
            }
        });
        if($pass)
            return response()->json(['message' => "Thành công !","success" => true]);
        else return response()->json(['message' => "Đã có lỗi xảy ra! Hãy thao tác lại","success" => false]);
    }

    public function delete(Request $request) {
        $id = $request->get("id","");
        try{
            if(!empty($id)) {
                $query = HoaDon::where('id',$id)->update(['deleted_at' => Carbon::now()]);
            }
            return response()->json(['message' => "Thành công !","success" => true]);
        }catch (\Exception $ex) {
            return response()->json(['message' => $ex,"success" => false]);
        }
        

    }

    public function getHoaDonByPhong(Request $request) {
        $ma_phong = $request->get("ma_phong",0);
        $month = $request->get("month",date('m'));

        $hd = HoaDon::select(['hoa_don.*','phong.ten_phong','nguoi_thue.ten'])->where(["ma_phong" => $ma_phong,"month" =>$month])->leftJoin('phong','hoa_don.ma_phong','phong.id')
        ->leftJoin('nguoi_thue','hoa_don.ma_nguoi_thue','nguoi_thue.id')->first();

        return response()->json(['data' => $hd], 200);

    }

    
}
