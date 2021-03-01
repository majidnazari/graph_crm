<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\Purchase;
use App\Student;
use App\StudentClassRoom;
use Illuminate\Support\Facades\DB;

use Exception;

class PurchaseController extends Controller
{
    public function index(){
        if(request()->getMethod()=='POST'){

        }

        $purchases = Purchase::where('is_deleted', false)
            // ->where('type', '!=', 'site_failed')
            ->with('user')
            ->with('student')
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('purchases.index',[
            'purchases' => $purchases,
            'msg_success' => request()->session()->get('msg_success'),
            'msg_error' => request()->session()->get('msg_error')
        ]);
    }

    public function create(Request $request)
    {
        $purchase = new Purchase();
        $students = Student::where('is_deleted', false)->where('archived',false)->where('banned',false)->get();
        $products = Product::where('is_deleted', false)->with('collection')->orderBy('name')->get();
        foreach($products as $index => $product){
            $products[$index]->parents = "-";
            if($product->collection) {
                $parents = $product->collection->parents();
                $name = ($parents!='')?$parents . "->" . $product->collection->name : $product->collection->name;
                $products[$index]->parents = $name;
            }
        }
        if($request->getMethod()=='GET'){
            return view('purchases.create', [
                'purchase'=>$purchase,
                'students'=>$students,
                'products'=>$products
            ]);
        }

        $student = Student::find($request->input('students_id'));

        $purchase->students_id = $request->input('students_id');
        $purchase->supporters_id = $student->supporters_id;
        $purchase->users_id = Auth::user()->id;
        $purchase->products_id = $request->input('products_id');
        $purchase->description = $request->input('description');
        $purchase->price = $request->input('price');
        $purchase->factor_number = $request->input('factor_number');
        $purchase->type = 'manual';
        $purchase->save();
        if($student){
            if ($student->supporters_id) {
                $student->own_purchases = $student->purchases()->where('supporters_id', $student->supporters_id)
                   ->where('is_deleted',false)->where('type','!=','site_failed')->count();
            }
            $student->other_purchases = $student->purchases()->where(function($query) use($student){
                if($student->supporters_id)$query->where('supporters_id','!=',$student->supporters_id)->orWhere('supporters_id',0);
            })->where('is_deleted',false)->where('type','!=','site_failed')->count();
            $student->today_purchases = $student->purchases()->where('created_at', '>=', date("Y-m-d 00:00:00"))->where('is_deleted',false)->where('type','!=','site_failed')->count();
            $student->save();
        }
        $request->session()->flash("msg_success", "پرداخت با موفقیت افزوده شد.");
        return redirect()->route('purchases');
    }

    public function edit(Request $request, $id)
    {
        $purchase = Purchase::where('id', $id)->where('is_deleted', false)->where('type', 'manual')->first();
        if($purchase==null){
            $request->session()->flash("msg_error", "پرداخت پیدا نشد!");
            return redirect()->route('purchases');
        }

        $students = Student::where('is_deleted', false)->where('banned', false)->get();
        $products = Product::where('is_deleted', false)->get();
        if($request->getMethod()=='GET'){
            return view('purchases.create', [
                'purchase'=>$purchase,
                'students'=>$students,
                'products'=>$products
            ]);
        }

        $student = Student::find($request->input('students_id'));
        $purchase->students_id = $request->input('students_id');
        $purchase->supporters_id = $student->supporters_id;
        $purchase->users_id = Auth::user()->id;
        $purchase->products_id = $request->input('products_id');
        $purchase->description = $request->input('description');
        $purchase->price = $request->input('price');
        $purchase->factor_number = $request->input('factor_number');
        $purchase->save();
        if($student){
            $student->today_purchases = $student->purchases()->where('created_at', '>=', date("Y-m-d 00:00:00"))->where('is_deleted',false)->where('type','!=','site_failed')->count();

            if($purchase->created_at < date("Y-m-d 00:00:00")){
               $student->today_purchases = $student->today_purchases > 0 ? $student->today_purchases -1 : $student->today_purchases;
            }
            $student->save();
        }

        $request->session()->flash("msg_success", "پرداخت با موفقیت ویرایش شد.");
        return redirect()->route('purchases');
    }

    public function delete(Request $request, $id)
    {
        $purchase = Purchase::where('id', $id)->where('is_deleted', false)->first();
        $student = Student::where('id',$purchase->students_id)->first();
        if($purchase==null){
            $request->session()->flash("msg_error", "پرداخت پیدا نشد!");
            return redirect()->route('purchases');
        }
        if($student){
            $student->today_purchases = $student->purchases()->where('created_at', '>=', date("Y-m-d 00:00:00"))->where('is_deleted',false)->where('type','!=','site_failed')->count();

            if($student->supporters_id){
                if($student->own_purchases > 0){
                    $student->own_purchases -= 1;
                }else{
                    $student->own_purchases = 0;
                }
            }else{
                if($student->other_purchases > 0){
                    $student->other_purchases -= 1;
                }else{
                    $student->other_purchases = 0;
                }
            }
            if($purchase->created_at >= date("Y-m-d 00:00:00")){
                if($student->today_purchases > 0){
                  $student->today_purchases -= 1;
                }else{
                  $student->today_purchases  = 0;
                }
            }
            $student->save();
        }

        $purchase->is_deleted = true;
        $purchase->save();

        $request->session()->flash("msg_success", "پرداخت با موفقیت حذف شد.");
        return redirect()->route('purchases');
    }

    //---------------------API------------------------------------
    public function apiDeletePurchases(Request $request) {
        $purchases = $request->input('purchases', []);
        $ids = [];
        $fails = [];
        foreach($purchases as $purchase){
            if(!isset($purchase['factor_number'])){
                $fails[] = $purchase;
                continue;
            }
            $purchaseObject = Purchase::where("factor_number", $purchase['factor_number'])->where('is_deleted', false)->where('type','!=','manual')->first();
            $student = Student::where('id',$purchaseObject->students_id)->first();
            if($student){
                $student->today_purchases = $student->purchases()->where('created_at', '>=', date("Y-m-d 00:00:00"))->where('is_deleted',false)->where('type','!=','site_failed')->count();

                if($student->supporters_id){
                    if($student->own_purchases > 0){
                        $student->own_purchases -= 1;
                    }else{
                        $student->own_purchases = 0;
                    }
                }else{
                    if($student->other_purchases > 0){
                        $student->other_purchases -= 1;
                    }else{
                        $student->other_purchases = 0;
                    }
                }
                if($purchaseObject->created_at >= date("Y-m-d 00:00:00")){
                    if($student->today_purchases > 0){
                      $student->today_purchases -= 1;
                    }else{
                      $student->today_purchases  = 0;
                    }
                }
                try{
                    $student->save();
                }catch(Exception $e){
                    $fails[] = $student;
                }
            }
            if(!$purchaseObject){
                $fails[] = $purchase;
                continue;
            }
            $purchaseObject->is_deleted = true;
            $purchaseObject->save();

            try{
                $purchaseObject->save();
                $ids[] = $purchaseObject->factor_number;
            }catch(Exception $e){
                $fails[] = $purchase;
            }
        }
        return [
            "deleted_ids" => $ids,
            "fails" => $fails
        ];
    }

    public function apiUnDeletePurchases(Request $request) {
        $purchases = $request->input('purchases', []);
        $ids = [];
        $fails = [];
        foreach($purchases as $purchase){

            if(!isset($purchase['factor_number'])){
                $fails[] = $purchase;
                continue;
            }
            $purchaseObject = Purchase::where("factor_number", $purchase['factor_number'])->where('is_deleted', true)->where('type','!=','manual')->first();
            if(!$purchaseObject){
                $fails[] = $purchase;
                continue;
            }
            $student = Student::where('id',$purchaseObject->students_id)->first();
            if($student){
                $student->today_purchases = $student->purchases()->where('created_at', '>=', date("Y-m-d 00:00:00"))->where('is_deleted',false)->where('type','!=','site_failed')->count();

                if($student->supporters_id){
                    $student->own_purchases += 1;
                }else{
                    $student->other_purchases += 1;
                }
                if($purchaseObject->created_at >= date("Y-m-d 00:00:00")){
                    $student->today_purchases += 1;
                }
                try{
                    $student->save();
                }catch(Exception $e){
                    $fails[] = $student;
                }
            }

            $purchaseObject->is_deleted = false;
            $purchaseObject->save();

            try{
                $purchaseObject->save();
                $ids[] = $purchaseObject->factor_number;
            }catch(Exception $e){
                $fails[] = $purchase;
            }
        }
        return [
            "deleted_ids" => $ids,
            "fails" => $fails
        ];
    }

    public function apiAddPurchases(Request $request){
        $purchases = $request->input('purchases', []);
        $ids = [];
        $fails = [];
        foreach($purchases as $purchase){
            if(!isset($purchase['woo_id']) || !isset($purchase['phone']) || !isset($purchase['price']) || !isset($purchase['factor_number'])){
                $fails[] = $purchase;
                continue;
            }
            $purchaseObject = Purchase::where("factor_name", $purchase['factor_number'])->first();
            if(!$purchaseObject)
                $purchaseObject = new Purchase;
            $product = Product::where('woo_id', $purchase['woo_id'])->with('classrooms')->first();
            $student = Student::where('phone', $purchase['phone'])->where('banned', false)->first();
            if($product == null || $student == null){
                $fails[] = $purchase;
                continue;
            }

            foreach($purchase as $key=>$value){
                if($key != 'woo_id' && $key != 'phone')
                    $purchaseObject->$key = $value;
            }
            $purchaseObject->products_id = $product->id;
            $purchaseObject->students_id = $student->id;
            $purchaseObject->supporters_id = $student->supporters_id;
            $purchaseObject->price = isset($purchase['price'])?$purchase['price']:0;
            $purchaseObject->users_id = 0;
            $purchaseSaved = false;
            try{
                $purchaseObject->save();
                $ids[] = $purchaseObject->factor_number;
                $purchaseSaved = true;
            }catch(Exception $e){
                $fails[] = $purchase;
            }
            if($student){
                if($student->supporters_id){
                    $student->own_purchases = $student->purchases()->where('supporters_id',$student->supporters_id)
                    ->where('is_deleted',false)->where('type','!=','site_failed')->count();
                }
                $student->other_purchases = $student->purchases()->where(function($query) use($student){
                    if($student->supporters_id) $query->where('supporters_id','!=',$student->supporters_id)->orWhere('supporters_id',0);;
                })
                ->where('is_deleted',false)->where('type','!=','site_failed')
                ->count();
                $student->today_purchases = $student->purchases()->where('created_at', '>=', date("Y-m-d 00:00:00"))->where('is_deleted',false)->where('type','!=','site_failed')->count();
                try{
                    $student->save();
                }catch(Exception $e){
                    // $fails[] = $student;
                }
            }
            if($purchaseSaved) {
                if($product->classrooms) {
                    foreach($product->classrooms as $classroom) {
                        $studentClassRoom = StudentClassRoom::where('students_id', $student->id)->where("class_rooms_id", $classroom->id)->first();
                        if($studentClassRoom==null) {
                            $studentClassRoom = new StudentClassRoom();
                            $studentClassRoom->students_id = $student->id;
                            $studentClassRoom->class_rooms_id = $classroom->id;
                            $studentClassRoom->users_id = -1;
                            try{
                                $studentClassRoom->save();
                            }catch(Exception $e){
                                // dd($e);
                            }
                        }
                    }
                }
            }
        }
        return [
            "added_ids" => $ids,
            "fails" => $fails
        ];
    }

    //---------------------API------------------------------------
    public function apiAddStudents(Request $request){
        $students = $request->input('students', []);
        $ids = [];
        $fails = [];
        foreach($students as $student){
            if(!isset($student['phone']) || !isset($student['last_name'])){
                $fails[] = $student;
                continue;
            }
            $studentObject = new Student;
            foreach($student as $key=>$value){
                $studentObject->$key = $value;
            }
            try{
                $studentObject->save();
                $ids[] = $studentObject->id;
            }catch(Exception $e){
                $fails[] = $student;
            }
        }
        return [
            "added_ids" => $ids,
            "fails" => $fails
        ];
    }
    public function test(){
        $col_ones = DB::table('new_students')
            ->select('phone')
            ->get();
        $students = [];
        $ids = [];
        foreach($col_ones as $item){
            $item->phone = '0'.substr($item->phone,0,-3);
            $students[] = Student::where('is_deleted',false)->where('phone',$item->phone)->first();
        }
        for($i = 0; $i< count($students);$i++){
            if($students[$i]){
                $ids[$i] = $students[$i]->id;
            }
        }
        foreach($ids as $id){
          $purchase = new Purchase;
          $purchase->products_id = 425;
          $purchase->users_id = 0;
          $purchase->type = 'manual';
          $purchase->description = 'همایش ۱۰ بهمن';
          $purchase->students_id = $id;
          $purchase->price = 0;
          $purchase->save();

        }
    }
}
