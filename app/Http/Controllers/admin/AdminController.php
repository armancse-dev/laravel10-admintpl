<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Validator;
use Image;
use Session;
// use Hash;
class AdminController extends Controller
{
    public function dashboard(){
        Session::put('page','dashboard');
        return view('admin.dashboard');
    }

    public function login(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'email' => 'required|email|max:255',
                'password' => 'required|max:30',
            ];

            $customMessages = [
                'email.required' => "Email is required",
                'email.email' => "Valid Email is required",
                'email.required' => "Password is required",
            ];

            $this->validate($request,$rules,$customMessages);

            if(Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']])){
                return redirect('admin/dashboard');
            }else{
                return redirect()->back()->with("error_message", "Invalid Email or Pasword");
            }
        }
        return view('admin.login');
    }

    public function logout(){
        Auth::guard('admin')->logout();
        return redirect('admin/login');
    }

    public function updatePassword(Request $request){
        Session::put('page','update-password');
        if($request->isMethod('post')){
            $data = $request->all();
            // check if current password is current
            if(Hash::check($data['current_password'],Auth::guard('admin')->user()->password)){
                //check if new password and confirm password are matching
                if($data['new_password']==$data['confirm_password']){
                    //update new password
                    Admin::where('id',Auth::guard('admin')->user()->id)->update(['password'=>bcrypt($data['new_password'])]);

                    return redirect()->back()->with('sucess_message','Password has been updated successfully');
                }else{
                    return redirect()->back()->with('error_message','New Password & Confirm password is not matching');
                }
            }else{
                return redirect()->back()->with('error_message','Your current password is incorrectt');
            }
        }
        return view('admin.update_password');
    }
    public function checkCurrentPassword(Request $request){
        $data = $request->all();
        if(Hash::check($data['current_password'],Auth::guard('admin')->user()->password)){
            return "true";
        }else{
            return "false";
        }
    }

    public function updateDetails(Request $request){
        Session::put('page','update-details');
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $rules = [
                'admin_name' => 'required|max:255',
                'admin_mobile' => 'required|numeric',
                'admin_img' => 'image',
            ];

            $customMessages = [
                'admin_name.required' => "Name is required",
                'admin_mobile.required' => "Mobile No. is required",
                'admin_mobile.numeric' => "Valid Mobile No. is required",
                'admin_imag.image' => "Valid Image is required",
            ];

            $this->validate($request,$rules,$customMessages);

            //upload admin image
            if($request->hasFile('admin_img')){
                $image_tmp = $request->file('admin_img');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    //generate New Image Name
                    $imageName = rand(111,9999).'.'.$extension;
                    $image_path = 'admin/images/photos/'.$imageName;
                    Image::make($image_tmp)->save($image_path);
                }
            }else if(!empty($data['current_image'])){
                $imageName = $data['current_image'];
            }else{
                $imageName = "";
            }

            //Update Admin Details
            Admin::where('email',Auth::guard('admin')->user()->email)->update(['name'=>$data['admin_name'],'mobile'=>$data['admin_mobile'],'image'=>$imageName]);
            return redirect()->back()->with('sucess_message','Admin Details Updated Successfully');
        }
        return view('admin.update_details');
    }
}
