<?php

namespace App\Http\Controllers;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()//لوحة التحكم
    {
        return response()->json([
            'owners_count'=>User::where('role','owner')->count(),
            'renter_count'=>User::where('role','renter')->count(),
            'Apartment_count'=>Apartment::count()
            ]);
    }
    //______________________________________________________________________
    public function pendingUsers()//المستخدمين بانتظار الموافقة
    {
        $user=User::with('profile')
        ->where('approval_status','pending')
        ->orderByDesc('created_at')
        ->get();

        return response()->json([
        'user'=>$user
        ]);
    }
    //______________________________________________________________________
    public function approvedUsers()//عرض المستخدمين الموافق عليهم
    {
        $user=User::with('profile')
        ->where('approval_status','approved')
        ->orderByDesc('created_at')
        ->get();

        return response()->json([
        'user'=>$user
        ]);
    }
    //______________________________________________________________________
    // public function rejectedUsers()//عرض المستخدمين المرفوضين
    // {
    //     $user=User::with('profile')->where('approval_status','rejected')->get();
    //     return response()->json([
    //     'user'=>$user
    //     ]);
    // }
    //______________________________________________________________________
    public function approveUser(int $user_id)//الموافقة على المستخدم
    {

        $user=User::with('profile')->where('approval_status','pending')->findOrFail($user_id);
        $user->update([
        'approval_status'=>'approved',
        ]);
        return response()->json([
        'message'=>'User approved successfully',
        ]);
    }
    //______________________________________________________________________
    public function rejecteUser(int $user_id)//رفض المستخدم
    {
        $user=User::with('profile')
        ->where('approval_status','pending')
        ->findOrFail($user_id);
        
        $user->update([
        'approval_status'=>'rejected',
        ]);
        $user->delete();

        return response()->json([
        'message'=>'User rejected successfully and deleted',
        ]);
    }
    //______________________________________________________________________
    public function deleteUser(int $user_id)//حذف المستخدم
    {
        $user=User::findorFail($user_id);
        if($user->role==='admin')
        {
            return response()->json([
            'message'=>'Cannot delete admin user',
            ],403);
        }
        $user->delete();

        return response()->json([
        'message'=>'User delete successfully',
        ]);
    }
    //______________________________________________________واجهة التفاصيل___

    // public function getFirstName($user_id)//جلب الاسم الاول
    // {
    //     $user=User::findorFail($user_id);
    //     return response()->json([
    //     'first_name'=>$user->profile->first_name
    //     ]);
    // }
    // //______________________________________________________________________
    // public function getLastName($user_id)//جلب الاسم الاخير
    // {
    //     $user=User::findorFail($user_id);
    //     return response()->json([
    //     'last_name'=>$user->profile->last_name
    //     ]);
    // }
    // //______________________________________________________________________
    // public function getPersonalPhoto($user_id)//جلب الصورة الشخصية
    // {
    //     $user=User::findorFail($user_id);
    //     return response()->json([
    //     'personal_photo'=>$user->profile->personal_photo
    //     ]);
    // }
    // //______________________________________________________________________
    // public function getAccount($user_id)
    // {
    //     $user=User::findorFail($user_id);
    //     return response()->json([
    //     'phone'=>$user->phone,
    //     'role'=>$user->role,
    //     'identity_photo'=>$user->profile->identity_photo
    //     ]);
    // }
}
