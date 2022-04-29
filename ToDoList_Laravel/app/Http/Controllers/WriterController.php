<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class WriterController extends Controller
{
    function  index()
    {


         $data =  User::get();  // select * from users

        return view('writers.index', ['data' => $data]);

    }



    function Create()
    {
        return view('writers.Create', ['title' => "Create Writer"]);
    }


    function Store(Request $request)
    {
        // code ......

        $data = $this->validate($request, [
            "name"     => 'required',
            "password" => "required|min:6|max:10",
            "email"    => "required|email|unique:users",
            "image"    => "required|image|mimes:png,jpg"
        ]);

        # Rename Image ....
        $FinalName = uniqid() . '.' . $request->image->extension();

        if ($request->image->move(public_path('/Images'), $FinalName)) {
            $data['image'] = $FinalName;
        }



        $data['password'] = bcrypt($data['password']);

        $op =  User::create($data);

        if ($op) {
            $message = 'Raw Inserted';
        } else {
           $message = 'Error Try Again';
        }
        # Set Message Session ....
        session()->flash('Message',$message);

        return redirect(url('/Writer'));

    }


    #############################################################################################################################

    function edit($id)
    {

        $title = "Edit Account";

        # Fetch Raw Data ...
        $data =  User::find($id);

        return view('writers.edit', ['title' => $title, 'data' => $data]);
    }

    #############################################################################################################################

    function update(Request $request, $id)
    {
        // code .....

        $data = $this->validate($request, [
            "name"     => 'required',
            "email"    => "required|email|unique:users,email," . $id,
            "image"    => "nullable|image|mimes:png,jpg"
        ]);


        # Fetch Raw Data ...
        $rawData = User::find($id);

        if ($request->hasFile('image')) {
            # Rename Image ....
            $FinalName = uniqid() . '.' . $request->image->extension();

            if ($request->image->move(public_path('/Images'), $FinalName)) {
                $data['image'] = $FinalName;

                unlink(public_path('Images/' . $rawData->image));
            }
        } else {
            $data['image'] = $rawData->image;
        }


        // update users ste name = $name , email = $email where id = $id

        $op =  User::where('id', $id)->update($data);

        if ($op) {
            $message = 'Raw Updated';
        } else {
            $message = "Error try again";
        }

     # Set Message Session ....
     session()->flash('Message',$message);

     return redirect(url('/Writer'));


    }

#############################################################################################################################

    function destroy($id)
    {
        // code ....

        # Fetch RaW Data
        $raw = User::find($id);

        # Delete Raw ....
        $op =   User::where('id', $id)->delete(); // delete from users where id = $id

        if ($op) {

            unlink(public_path('Images/' . $raw->image));

            $message = 'Raw Deleted';
        } else {
            $message = 'Error Try Again';
        }

             # Set Message Session ....
             session()->flash('Message',$message);

             return redirect(url('/Writer'));
    }
#############################################################################################################################

function login(){

    return view('writers.login',['title' => "Login Page"]);

}

#############################################################################################################################

function DoLogin(Request $request){
    // code ....

     $data = $this->validate($request, [
           "email"    => "required|email",
           "password" => "required|min:6|max:10"
     ]);


       if(auth()->attempt($data)){

          return redirect(url('/Writer'));

       }else{

        session()->flash('Message',"Error In Your Data Try Again");

        return back();

       }

}


#############################################################################################################################

  function logout(){

      auth()->logout();
      return redirect(url('/Login'));

    }

}
