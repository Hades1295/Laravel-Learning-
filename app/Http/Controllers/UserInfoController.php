<?php

namespace App\Http\Controllers;

use App\Models\Crud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $users = Crud::latest()->paginate(5);
        return view('users.index',compact('users'))
        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create'); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       try{ 
        $request->validate([
            'name'=>'required',
            'email'=>'required',
            'address'=>'required',
        ]);
        $token = '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';
        $response =  Http::withToken($token)->post('https://api.sendgrid.com/v3/validations/email',[
            'email' => "$request->all('email')['email']",
            'source' => 'signup',
        ]);
        // $response = 
        //     Http::post('https://api.sendgrid.com/v3/validations/email', [
        //         'headers' => [
        //             'Authorization' => 'Bearer '.$token,
        //             'Accept' => 'application/json',
        //         ],
        //             'email' => "$request->all('email')['email']",
        //             'source' => 'signup',
        //     ]);
            dd($response);exit;
            Crud::create($request->all());
            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        }catch(\Throwable $th){
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Crud  $crud
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = Crud::find($id);
        return view('users.show', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Crud  $crud
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = Crud::find($id);
        return view('users.edit', compact('users',$users));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Crud  $crud
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required',
            'address'=>'required'
        ]);
        try {
            Crud::where('id', $id)->update($request->except(['_token']));
            return redirect()->route('users.index')
                ->with('success', 'User Updated successfully.');
        } catch (\Throwable $th) {
             dd($th);
            return false;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Crud  $crud
     * @return \Illuminate\Http\Response
     */
    public function destroy(Crud $id)
    {  
        try {
            $id->delete();
            return redirect()->route('users.index')
            ->with('success', 'User Deleted successfully.');
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}
