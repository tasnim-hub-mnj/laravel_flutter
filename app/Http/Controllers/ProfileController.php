<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function UpdateProfile(UpdateProfileRequest $request)//تعديل
    {
            $user_id=Auth::user()->id;
            $profile=Profile::findOrFail($user_id);//رقم المستخدم هو نفس رقم البروفايل
            $data=[$request->validated()];

            if($request->hasFile('personal_photo'))
            {
                if ($profile->personal_photo)
                {
                    Storage::disk('public')->delete($profile->personal_photo);
                }
                $path1 = $request->file('personal_photo')->store('my profile', 'public');
                $profile->personal_photo = $path1;
                $data['personal_photo']=$path1;
            }
            if ($request->hasFile('identity_photo'))
            {
                if ($profile->identity_photo)
                {
                Storage::disk('public')->delete($profile->identity_photo);
                }
                $path2 = $request->file('identity_photo')->store('my identity', 'public');
                $profile->identity_photo = $path2;
                $data['identity_photo']=$path2;
            }
            $profile->update($data);

            return response()->json([
                'message'=>'Profile updated successfully',
                'profile'=>$profile,
            ],200);
    }
    //_____________________________________________________________
    public function getUserProfile()//طباعة بروفايل المستخدم الحالي
    {
        $user_id=Auth::user()->id;
        $user = User::findOrFail($user_id);
        $profile = $user->profile;

        return response()->json([
            'your profile: '=>
            [
                'personal_photo'=>$profile->personal_photo,
                'first_name'=>$profile->first_name,
                'last_name'=>$profile->last_name,
                'birth_date'=>$profile->birth_date,
                'identity_photo'=>$profile->identity_photo,
            ]
        ], 200);
    }
}



