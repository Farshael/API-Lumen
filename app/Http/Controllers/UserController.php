<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
{
    $this->middleware('auth:api');
}

    public function index()
    {
        try {
            // ambil data yang mau di tampilkan
            $data = User::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'username' => 'required',
                'email' => 'required',
                'role' => 'required',
                'password' => 'required',
                
            ]);

            $data = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password)

            ]);
            return ApiFormatter::sendResponse(200,'success',$data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400,'bad request' , $err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = User::where('id', $id)->first();

            if(is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            }else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            }
            
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
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
        try {
            $this->validate($request, [
                'username' => 'required',
                'email' => 'required',
                'role' => 'required',
                'password' => 'required',
            ]);
    
            $checkProses = User::where('id', $id)->update([
                'username' => $request->username,
                'email' => $request->email,
                'role' => $request->role, 
                'password' => Hash::make($request->password)
            ]);
    
            if($checkProses) {
                $data = User::find($id);
                return ApiFormatter::sendResponse(200, 'succes', $data);
            }else {
                return ApiFormatter::sendResponse(400, 'bad request ',  'failed to update data!');
                
            }
        } catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $checkProses = User::where('id', $id)->delete();

            return ApiFormatter::sendResponse(200, 'success', 'Success to delete data!');
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
    }
    }

    public function trash()
    {
        try {
            $data = User::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
    }
}

public function restore($id)
{
    try {
     
        $checkProses = User::onlyTrashed()->where('id', $id)->restore();

    if($checkProses){
        $data = User::find($id);
        return ApiFormatter::sendResponse(200, 'success', $data);
    }else {
        return ApiFormatter::sendResponse(400, 'bad request', 'failed to restore data!');
    }

    }catch(\Exception $err) {
        return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
}
}

public function deletePermanent($id)
{
    try {
        $checkProses = User::onlyTrashed() -> where('id', $id)-> forceDelete();

        return ApiFormatter::sendResponse(200, 'success', 'Berhasil menghapus permanen data!');
    } catch (\Exception $err) {
        return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
    }
}

public function login(Request $request)
{
    try {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        // mendapatkan data user berdasarkan email yang di gunakan untuk login

        if(!$user){
            return ApiFormatter::sendResponse(400, false, 'Login failed! user doesnt exist');
        } else {
            $isValid = Hash::check($request->password, $user->password);

            if(!$isValid) {
                return ApiFormatter::sendResponse(400, false, 'login failed! password doesnt match');
            } else {
                $generateToken = bin2hex(random_bytes(40));


                $user->update([
                    'token' => $generateToken
                ]);

                return ApiFormatter::sendResponse(200,'login successfully', $user);
            }
        }
    } catch (\Exception $e){
        return ApiFormatter::sendResponse(400, false, $e->getMessage());
    }
    }


    public function logout(Request $request)
    {
        try {
            $this-> validate($request, [
                'email' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ApiFormatter::sendResponse(400, 'login failed! User Doesnt Exists');
            } else {
                if (!$user->token) {
                    return ApiFormatter::sendResponse(400, 'Logout Failed! User Doesnt Login Sciene');
                } else {
                    $logout = $user->update(['token' => null]);

                    if ($logout) {
                        return ApiFormatter::sendResponse(200, 'Logout Successfuly');
                    }
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, $e->getMessage());
        }
    }
}