<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    function __construct()
    {

          $this->middleware(['cannotDeleteExpiredTask'],['except' => ["index","create","store"]]);
    }
    public function index()
    {
        $currentid = auth()->user()->id;        
        $data = DB :: table('tasks')->join('writers', 'writers.id','=','tasks.writer_id')->select('tasks.*','writers.name  as WriterName')->orderby('id','desc')->where('writer_id',$currentid)->get();

         return view('tasks.index',['data' => $data]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tasks.create',['title' => "Create Task "]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request,[
            "title"   => "required|min:10",
            "content" => "required|min:50",
            "start_date" => "required|date|after_or_equal:today",
            "end_date" => "required|date|after_or_equal:today",
            "image"   => "required|image|mimes:png,jpg"
        ]);

        # SET ADDED BY ID .....
        $data['writer_id'] = auth()->user()->id;

        $data['start_date'] = $request->start_date;
        $data['end_date'] = $request->end_date;


         # Rename Image ....
         $FinalName = uniqid() . '.' . $request->image->extension();

         if ($request->image->move(public_path('/Tasks'), $FinalName)) {
             $data['image'] = $FinalName;
         }


         $op =   DB :: table('tasks')->insert($data);

         if($op){
             $message = "Raw Inserted";
         }else{
             $message = "Error Try Again";
         }

         session()->flash('Message',$message);

         return redirect(url('/Task'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $op = DB :: table('tasks')->where('id',$id)->delete();

        if ($op) {
            $message = "Raw Removed";
        } else {
            $message = "Error Try Again";
        }

        session()->flash('Message', $message);

        return redirect(url('/Task'));

    }
    public function message(){

    }
}
