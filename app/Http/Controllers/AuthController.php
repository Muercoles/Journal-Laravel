<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class AuthController extends Controller
{
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
    public function getAllUsers()
    {
        $users = User::select('id', 'name')->get();

        return response()->json([
            'status' => 'success',
            'users' => $users,
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);

    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'surname'=> 'required|string|min:6',
            'birthday'=> 'required|string|min:6',
            'classroom'=> 'required|string|min:6',
            'phone'=> 'required|string|min:6'
        ]);
//        if ($request->has('image')) {
//            $dir = $_SERVER['DOCUMENT_ROOT'];
//            $year = date('Y');
//            $month = date('m');
//            $basePath = $dir . '/uploads/' . $year . '/' . $month;
//            if (!file_exists($basePath)) {
//                mkdir($basePath, 0777, true);
//            }
//            $filename = uniqid() . '.' . $request->file("image")->getClientOriginalExtension();
//            $fileSave = $basePath . '/' . $filename;
//            $this->image_resize(700, 700, $fileSave, 'image');
//            $input["image"] = $year . '/' . $month . '/' . $filename;
//        } else {
//            $input["image"] = null;
//        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'surname' => $request->surname,
            'birthday'=> $request->birthday,
            'classroom'=> $request->classroom,
            'phone'=> $request->phone,
            'image' => $request->image
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

   public function image_resize($width, $height, $path, $inputName)
    {
        list($w, $h) = getimagesize($_FILES[$inputName]['tmp_name']);
        $maxSize = 0;
        if (($w > $h) and ($width > $height))
            $maxSize = $width;
        else
            $maxSize = $height;
        $width = $maxSize;
        $height = $maxSize;
        $ration_orig = $w / $h;
        if (1 > $ration_orig)
            $width = ceil($height * $ration_orig);
        else
            $height = ceil($width / $ration_orig);

        $imgString = file_get_contents($_FILES[$inputName]['tmp_name']);
        $image = imagecreatefromstring($imgString);

        $tmp = imagecreatetruecolor($width, $height);
        imagecopyresampled($tmp, $image,
            0, 0,
            0, 0,
            $width, $height,
            $w, $h);

        switch ($_FILES[$inputName]['type']) {
            case 'image/jpeg':
                imagejpeg($tmp, $path, 30);
                break;
            case 'image/png':
                imagepng($tmp, $path, 0);
                break;
            case 'image/gif':
                imagegif($tmp, $path);
                break;
        }
        return $path;

        imagedestroy($image);
        imagedestroy($tmp);
    }
}
