<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    public function adminLogout (Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
    public function adminProfile()
    {
        $id = Auth::user()->id;

        $profileData = User::findOrFail($id);

        return view('admin.admin_profile', [
            'profileData' => $profileData,
        ]);
    }
    public function profileStore(Request $request)
    {
        $id = Auth::user()->id;
        $data = User::findOrFail($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        $oldPhotoPath = $data->photo;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/user_images'), $fileName);
            $data->photo =  $fileName;

            if ($oldPhotoPath && $oldPhotoPath != $fileName) {
                $this->deleteOldImage($oldPhotoPath);
            }
        }
        $data->save();

        $notification = [
            'message' => 'Profile updated successfully',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.profile')->with($notification);


    }
    private function deleteOldImage(string $oldPhotoPath): void
    {
        if (file_exists(public_path('upload/user_images/' . $oldPhotoPath))) {
            unlink(public_path('upload/user_images/' . $oldPhotoPath));
        }
    }

    public function passwordUpdate(Request $request)
    {

    }

}
