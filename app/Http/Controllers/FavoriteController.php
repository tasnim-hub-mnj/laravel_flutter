<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function addToFavorites(int $apartmentId)//اضافة الى المفضلة
    {
        try
        {
            Apartment::findOrFail($apartmentId);
            Auth::user()->favoritesApartment()->syncWithoutDetaching($apartmentId);
            return response()->json([
                'message'=>'Added To Favorite List'
            ],201);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'error'=>'the apartment is not found'
            ],404);
        }
    }
    //____________________________________________________
    public function removeFromFavorites(int $apartmentId)//حذف من المفضلة
    {
        try
        {
            Apartment::findOrFail($apartmentId);
            Auth::user()->favoritesApartment()->detach($apartmentId);
            return response()->json([
                'message'=>'Removed From Favorite List'
            ],200);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'error'=>'the apartment is not found'
            ],404);
        }
    }
    //____________________________________________________
    public function getFavoritesApartments()//جلب المفضلة
    {
        $apartments_favorite=Auth::user()->favoritesApartment;
        return response()->json([
            'message'=>'Favorite List :',
            $apartments_favorite
        ],200);
    }
    //____________________________________________________
    public function countFavorites()//عدد المفضلة
    {
        $count_favorites=Auth::user()->favoritesApartment->count();
        return response()->json([
            'message'=>'Number of Favorite Apartments :',
            'count'=>$count_favorites
        ],200);
    }
    //____________________________________________________
    public function getAllFavoritesICAR()//جلب المفضلة مع المدينةوالمنطقةوالتقييم المتوسط و الصورة
    {
        $favorites=Auth::user()->favoritesApartment()->get();
        $favorites_rating=$favorites->map(function($apartment)
        {
            $apartment->makeHidden('pivot');

            $apartment_data=$apartment->toArray();
            $apartment_data['image']=$apartment->image;
            $apartment_data['city']  = $apartment->city;
            $apartment_data['area']  = $apartment->area;
            $apartment_data['average_rating']=$apartment->ratings()->avg('rating_value');
            return $apartment_data;
        });
        return response()->json([
            'message'=>'Favorite Apartments with Image, City, Area, and Average Rating :',
            $favorites_rating
        ],200);
    }
    //____________________________________________________
    public function getOneFavoriteWithAllDetailed(int $apartmentId)//جلب مفضلة واحدة مع جميع التفاصيل
    {
        $favorites=Auth::user()->favoritesApartment()->get();
        $favorite=$favorites->firstWhere('id',$apartmentId);
        if(!$favorite)
        {
            return response()->json([
                'error'=>'the apartment is not in favorite list'
            ],404);
        }
        $apartment_data=$favorite->toArray();
        $apartment_data['average_rating']=$favorite->ratings()->avg('rating_value');
        $favorite_detailed=[
            // 'id'=>$apartment_data['id'],
            'city'=>$apartment_data['city'],
            'area'=>$apartment_data['area'],
            'average_rating'=>$apartment_data['average_rating'],
            'space'=>$apartment_data['space'],
            'size'=>$apartment_data['size'],
            'description'=>$apartment_data['description'],
            'price'=>$apartment_data['price'],
            'is_available'=>$apartment_data['is_available'],
            'ratings'=>$favorite->ratings,
        ];

        return response()->json([
            'message'=>'Detailed Favorite Apartment :',
            $favorite_detailed
        ],200);
    }
    //____________________________________________________
    // public function getAllFavoriteWithAllDetailed()//جلب المفضلة مع جميع التفاصيل
    // {
    //     $favorites=Auth::user()->favoritesApartment()->with('city','area')->get();
    //     $favorites_detailed=$favorites->map(function($apartment)
    //     {
    //         $apartment_data=$apartment->toArray();
    //         $apartment_data['average_rating']=$apartment->ratings()->avg('rating_value');
    //         return [
    //             // 'id'=>$apartment_data['id'],
    //             'city'=>$apartment_data['city']['name'],
    //             'area'=>$apartment_data['area']['name'],
    //             'average_rating'=>$apartment_data['average_rating'],
    //             'space'=>$apartment_data['space'],
    //             'size'=>$apartment_data['size'],
    //             'description'=>$apartment_data['description'],
    //             'price'=>$apartment_data['price'],
    //             'is_available'=>$apartment_data['is_available'],
    //         ];
    //     });
    //     return response()->json([
    //         'message'=>'Detailed Favorite Apartments :',
    //         $favorites_detailed
    //     ],200);
    // }
}
