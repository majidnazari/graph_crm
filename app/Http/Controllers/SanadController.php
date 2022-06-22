<?php

namespace App\Http\Controllers;

use App\Sanad;
use App\Group;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Log;

class SanadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $total_debtor=0;
        $total_creditor=0;
       // $date_tmp=$georgianCarbonDate=Jalalian::fromFormat('Y-m-d', '1401-03-28')->toCarbon();       
        $sanad_month=[1,2,3,4,5,6,7,8,9,10,11,12];
        $sanad_year=[];
        $year =(int)jdate()->format("Y");//Carbon::now()->format("Y");
        $sanad_year= range($year-5, $year+5);
        $sanads = Sanad::all();
        $supports = User::where('is_deleted', false)->where('groups_id', 2)->get();
        // foreach($sanads as $sanad){
        //     if($sanad->type > 0){
        //         $total_debtor+=$sanad->total_cost;
        //     }
        //     else
        //     {
        //         $total_creditor+=$sanad->total_cost;
        //     }
        // }
       
        return view('sanads.index',[
            'sanads' => $sanads,
            'supporters' =>$supports,
            'sanad_year' => $sanad_year,
            'sanad_month' => $sanad_month,            
            //'date_tmp'=>$date_tmp,   
            // 'total_creditor' => $total_creditor,
            // 'total_debtor' => $total_debtor,
            'msg_success' => request()->session()->get('msg_success'),
            'msg_error' => request()->session()->get('msg_error')
        ]);
    }

    public function indexWithSearch(Request $request){
        //Log::info("the method is:"). $request->all(); 
        //return $request->input('supporter_id');
        $req =  request()->all();
        if (!isset($req['start'])) {
            $req['start'] = 0;
            $req['length'] = 10;
            $req['draw'] = 1;
        }
        $total_debtor=0;
        $total_creditor=0;
        $data = [];
        $first_day_of_this_month = jdate()->format('Y').'-'.jdate(strtotime('2021-07-20'))->format('n').'-01';//it shows full first  date like 1401-03-01 
        $last_day_of_this_month = jdate()->format('Y-n-t'); //it shows full  end of date like 1401-03-31 
        $first_day=explode('-',$first_day_of_this_month);
        $last_day=explode('-' , $last_day_of_this_month);
        if($request->input('flag')!="")
        {
            
            $sanad_year= $request->input('year');
            $sanad_month=$request->input('month') < 10 ? '0'.$request->input('month') : $request->input('month'); 
            $sanad_date_from=$sanad_year.'-'.$sanad_month.'-'.$first_day[2] ;
            $sanad_date_to=$sanad_year.'-'.$sanad_month.'-'.$last_day[2] ;
            //$sanad_date_from=Jalalian::fromFormat('Y-m-d',$sanad_date_from)->toCarbon();
            //$sanad_date_to=Jalalian::fromFormat('Y-m-d',$sanad_date_to)->toCarbon();
           //$tmp=Jalalian::fromFormat('Y-m-d',$first_lim)->toCarbon();
            return      $sanad_date_to ;
        }
       // $date_tmp=$georgianCarbonDate=Jalalian::fromFormat('Y-m-d', '1401-03-28')->toCarbon();       
        // $sanad_month=[1,2,3,4,5,6,7,8,9,10,11,12];
        // $sanad_year=[];
        // $year =(int)jdate()->format("Y");//Carbon::now()->format("Y");
        // $sanad_year= range($year-5, $year+5);
        //$sanads =  Sanad::all();
        $sanads = Sanad::where('id','>',  1);
        //$count=count($sanads);
        if ($request->input('supporter_id') != 0)
        {
            //Log::info("add to log");              
            $support_id=$request->input('supporter_id');
            $sanads=$sanads->where('supporter_id',$support_id);
        }   
        if ($request->input('month') != 0  && $request->input('year') != 0)
        {
            $sanad_year= $request->input('year');
            $sanad_month=$request->input('month') < 10 ? '0'.$request->input('month') : $request->input('month'); 
            $sanad_date_from=$sanad_year.'-'.$sanad_month.'-'.$first_day[2] ;
            $sanad_date_to=$sanad_year.'-'.$sanad_month.'-'.$last_day[2] ;
            $sanad_date_from=Jalalian::fromFormat('Y-m-d',$sanad_date_from)->toCarbon();
            $sanad_date_to=Jalalian::fromFormat('Y-m-d',$sanad_date_to)->toCarbon();
            //$sanads= $sanads->whereBetween('updated_at',array($sanad_date_from, $sanad_date_to));
            $sanads= $sanads->where('updated_at','>=',$sanad_date_from);      
            $sanads= $sanads->where('updated_at','<=',$sanad_date_to);   
           //$tmp=Jalalian::fromFormat('Y-m-d',$first_lim)->toCarbon();
            // $date_data_to=$sanad_year.'-'.$sanad_month.'-'.$last_day_of_this_month ; 
            // $milady_date_from=Jalalian::fromFormat('Y-m-d',  $sanad_date_from)->toCarbon();
            // $milady_date_to=Jalalian::fromFormat('Y-m-d',  $date_data_to)->toCarbon(); 
           // $sanads= $sanads->whereBetween('updated_at',array($milady_date_from,$milady_date_to));
           //Log::info("the from date is:$milady_date_from, and to date is:$milady_date_to");     
               
        }
        $sanads=$sanads->with('supporter')
        ->skip($req['start'])
        ->take($req['length'])
        ->get();
        $supports = User::where('is_deleted', false)->where('groups_id', 2)->get();      

        $total_get_price=0;
        $total_give_price=0;   
        foreach ($sanads as $index => $item) {
            $get_price=0;
            $give_price=0;
            if($item->type>0){
                $get_price=$item->total;
                $total_get_price+=$item->total;
            }
            if($item->type<0){
                $give_price=$item->total;
                $total_give_price+=$item->total;
            }

            $btn = '<a class="btn btn-primary" href="' . route('sanad_edit', $item->id) . '"> ویرایش</a>';
                  //  <a class="btn btn-danger" href="' . route('merge_students_delete', $item->id) . '"> حذف </a>';
            $data[] = [                
                "row" => $index + 1,
                "id" => $item->id,
                "supporter" => $item->supporter->first_name.' ' .$item->supporter->last_name ,
                "number" => $item->number,
                "description" => $item->description,
                "updated_at" =>jdate($item->updated_at)->format("Y/m/d") ,
                "total_cost" => $item->total_cost,
                "total_get" => $item->type > 0 ? $item->total : 0,
                "total_give" => $item->type < 0 ? $item->total : 0,
                "supporter_percent" =>  $item->type > 0 ?  number_format(ceil($item->total * $item->supporter_percent / 100)) : "",//// $item->supporter_percent,
                "end" => $btn
                // "auxilary_students_id" => (($item->auxilaryStudent) ? $item->auxilaryStudent->first_name : '-'). " ". (($item->auxilaryStudent) ? $item->auxilaryStudent->last_name : '-'). "-".(($item->auxilaryStudent) ? $item->auxilaryStudent->phone : '-'),
                // "second_auxilary_students_id" =>(($item->secondAuxilaryStudent) ? $item->secondAuxilaryStudent->first_name : '-'). " ". (($item->secondAuxilaryStudent) ? $item->secondAuxilaryStudent->last_name : '-'). "-".(($item->secondAuxilaryStudent) ? $item->secondAuxilaryStudent->phone : '-') ,
                // "third_auxilary_students_id" => (($item->thirdAuxilaryStudent) ? $item->thirdAuxilaryStudent->first_name : '-'). " ". (($item->thirdAuxilaryStudent) ? $item->thirdAuxilaryStudent->last_name : '-'). "-".(($item->thirdAuxilaryStudent) ? $item->thirdAuxilaryStudent->phone : '-'),
               
            ];
        }
        $result = [
            //"draw" => $req['draw'],
            "data" => $data,
            "request" => $request->all(),
            //  "recordsTotal" => $count,
            //  "recordsFiltered" => 80
        ];

        return $result;
       
        // return view('sanads.index',[
        //     'sanads' => $sanads,
        //     'supporters' =>$supports,
        //     'sanad_year' => $sanad_year,
        //     'sanad_month' => $sanad_month, 
        //     'requestall' => $request->all(),
        //     //'date_tmp'=>$date_tmp,   
        //     // 'total_creditor' => $total_creditor,
        //     // 'total_debtor' => $total_debtor,
        //     'msg_success' => request()->session()->get('msg_success'),
        //     'msg_error' => request()->session()->get('msg_error')
        // ]);
    }

    // public function indexWithSearch(Request $request)
    // {
    //     $first_day_of_this_month = jdate()->format('Y').'-'.jdate()->format('n').'-01';
    //     $last_day_of_this_month = jdate()->format('Y-n-t');
    //     $total_debtor=0;
    //     $total_creditor=0;
    //     $sanads = Sanad::all();
    //     $date_from='';
    //     $date_to='';
    //     $supports="";
    //     $milady_date_from=0;
    //     $milady_date_to=0;
    //     if($request->input('supporters_id'))
    //     {
    //         $sanads=$sanads->where('supporter_id',$request->supporter_id);
    //     }        
    //     if($request->input('sanad_year'))
    //     {
    //         $date_from.=$request->input('sanad_year').'-';           
    //     }
    //     if($request->input('sanad_month'))
    //     {
    //         $date_from.=$request->input('sanad_month').'-'; 
    //         $date_from.=$first_day_of_this_month ;
    //         $date_to.=$last_day_of_this_month ; 
    //         $milady_date_from=Jalalian::fromFormat('Y-m-d',  $date_from)->toCarbon();
    //         $milady_date_to=Jalalian::fromFormat('Y-m-d',  $$date_to)->toCarbon(); 
    //         $sanads= $sanads->whereBetween('updated_at',array($milady_date_from,$milady_date_to));      
    //     }
        
        
        
    //     $supports = User::where('is_deleted', false)->where('groups_id', 2)->get();
    //     // foreach($sanads as $sanad){
    //     //     if($sanad->type > 0){
    //     //         $total_debtor+=$sanad->total_cost;
    //     //     }
    //     //     else
    //     //     {
    //     //         $total_creditor+=$sanad->total_cost;
    //     //     }
    //     // }
       
    //     return view('sanads.index',[
    //         'sanads' => $sanads,
    //         'supporters' =>$supports,
    //         'milady_date_to'=> $milady_date_to,
    //         'milady_date_from' =>$milady_date_from,
    //         // 'total_creditor' => $total_creditor,
    //         // 'total_debtor' => $total_debtor,
    //         'msg_success' => request()->session()->get('msg_success'),
    //         'msg_error' => request()->session()->get('msg_error')
    //     ]);
    // }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $sanad = new Sanad;
        
        if($request->getMethod()=='GET'){
            $supportGroupId = Group::getSupport();
            if ($supportGroupId)
                $supportGroupId = $supportGroupId->id;
            $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
            return view('sanads.create', [
                "sanad"=>$sanad,
                "supports"=>$supports,
            ]);
        }

        $sanad->supporter_id = $request->input('supporter_id');
        $sanad->number = $request->input('number');
        $sanad->description = $request->input('description');
        $sanad->total = (int)$request->input('total', 0);
        $sanad->total_cost = (int)$request->input('total_cost', 0);
        $sanad->supporter_percent = (int)$request->input('supporter_percent', 0);
        $sanad->type = $request->type && $request->type === "on" ? 1 : -1;
        $sanad->user_id = Auth::user()->id;
        $sanad->save();

        $request->session()->flash("msg_success", "سند با موفقیت افزوده شد.");
        return redirect()->route('sanads');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sanad  $sanad
     * @return \Illuminate\Http\Response
     */
    public function show(Sanad $sanad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sanad  $sanad
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {   

        $sanad=Sanad::find($id);
        $supportGroupId = Group::getSupport();
        if ($supportGroupId)
            $supportGroupId = $supportGroupId->id;
        $supports = User::where('is_deleted', false)->where('groups_id', $supportGroupId)->get();
        return view('sanads.edit', [
            "sanad"=>$sanad,
            "supports"=>$supports,
        ]);
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sanad  $sanad
     * @return \Illuminate\Http\Response
     */
   // public function update(Request $request, int $sanad)
    public function update(Request $request , int $sanad_id )
    {
             //dd($sanad_decode->id);
           // dd($request->all());
        //$sanad_decode=json_decode($sanad_json);
        //$sanad_id=$sanad_decode->id;
        $sanad= Sanad::find($sanad_id);
        if($sanad)
        {
            $sanad->fill($request->all());
            $sanad->type= $request->type && $request->type === "on" ? 1 : -1;
            $sanad->save();
        }
        $request->session()->flash("msg_success", "سند با موفقیت افزوده شد.");
        return redirect()->route('sanads');
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sanad  $sanad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sanad $sanad)
    {
        //
    }
}
