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
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use DB;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Api
 */
class KhachHangController extends BaseController
{
    
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $query = [];
        if(isset($searchParams['id'])) {
            $query = KhachHang::find($searchParams['id']);
        }
        else{
            if(isset($searchParams['search'])) {
                $query = KhachHang::where('ten','like', '%'.$searchParams['search'].'%')->get()->toArray();
            }
            else
                $query = KhachHang::all()->toArray();
        }

        return response()->json(['data' => $query], 200);
    }

    public function store(Request $request) {
        $id = $request->get("id","");
        $ten = $request->get("ten","");
        $sdt = $request->get("sdt",0);
        $ngay_sinh = $request->get("ngay_sinh","");
        


        try {

            if(empty($id)) {}
            else {
                $query = KhachHang::where('id',$id)->update(['ten' => $ten,'sdt'=>$sdt,'ngay_sinh' => $ngay_sinh]);
            }

            return response()->json(['message' => "Thành công !","success" => true]);
        }
        catch (\Exception $e){
            return response()->json(['message' => $e,"success" => false]);
        }
    }

    public function getForBirthDays(Request $request) {
        $currentDate = now(); // Ngày hiện tại
        $futureDate = now()->addDays(7); // Ngày sau 7 ngày
        $customers = KhachHang::whereRaw("CONCAT(YEAR('$currentDate'),'-', DATE_FORMAT(ngay_sinh, '%m-%d')) BETWEEN  CONCAT(YEAR('$currentDate'),'-',DATE_FORMAT('$currentDate', '%m-%d')) AND CONCAT(YEAR('$currentDate'),'-',DATE_FORMAT(DATE_ADD('$currentDate', INTERVAL 7 DAY), '%m-%d'))")
        ->get();
        return response()->json(['data' => $customers], 200);
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
