<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//use DB;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Illuminate\Support\Arr;


class TripController extends Controller
{
    public function index($page)
    {
        //編碼
        $headers = array('Content-Type' => 'application/json; charset=utf-8');


        $data = DB::table('trip.itinerary')

            ->leftJoin('trip.tour_group', 'itinerary.tripid', '=', 'tour_group.tripId')
            ->leftJoin('trip_tag', 'itinerary.tripid', '=', 'trip_tag.tripId')
            ->leftJoin('trip_agency', 'itinerary.agencyid', '=', 'trip_agency.agencyId')
            ->leftJoin('itinerary_tag', 'itinerary_tag.tagId', '=', 'trip_tag.tagId')

            //WHERE tour_group.tourGroupId IS NOT NULL
            ->where('tour_group.tourGroupId', '<>', null)
            ->orderBy('itinerary.oriPrice', 'desc')
            ->orderBy('tour_group.tripScore', 'desc')

            ->select(
                'itinerary.tripid',
                'itinerary.tripName',
                'itinerary.totalDay',
                'itinerary.oriPrice',
                'itinerary.description',

                'itinerary_tag.tagName',
                'itinerary_tag.tagDescription',
                'itinerary_tag.tagCategory',

                'tour_group.tourGroupId',
                'tour_group.startDate',
                'tour_group.endDate',
                'tour_group.totalPeople',
                'tour_group.reserve',
                'tour_group.nowPrice',
                'tour_group.tripScore',

                'trip_agency.agencyName'
            );

        //行程
        $query = $data
            ->select('itinerary.tripid', 'itinerary.tripName', 'itinerary.totalDay', 'itinerary.oriPrice', 'itinerary.description', 'trip_agency.agencyName')
            ->groupBy(['itinerary.tripid'])
            ->get();

        //旅團資料
        $query2 = $data
            ->select('itinerary.tripid', 'tour_group.tourGroupId', 'tour_group.startDate', 'tour_group.endDate', 'tour_group.totalPeople', 'tour_group.reserve', 'tour_group.nowPrice', 'tour_group.tripScore')
            ->groupBy(['tour_group.tourGroupId'])
            ->get();

        //行程分數->用旅團的分數計算平均值
        $query2_score = $data
            ->select('tour_group.tripScore')
            ->avg('tour_group.tripScore');
        //->get();

        //行程標籤
        $query3 = $data
            ->select('itinerary.tripid', 'itinerary_tag.tagName', 'itinerary_tag.tagDescription', 'itinerary_tag.tagCategory')
            ->groupBy(['itinerary_tag.Id'])
            ->distinct()
            ->get();


        /**
         * 建立資料格式
         *
         * @
         */

        $collect = collect();

        foreach ($query as $row) {
            $b = new Itinerary();
            $b->tripid = $row->tripid;
            $b->tripName = $row->tripName;
            $b->totalDay = $row->totalDay;
            $b->oriPrice = $row->oriPrice;
            $b->description = $row->description;
            $b->agencyName = $row->agencyName;
            $b->tripScore = number_format($query2_score, 2);

            //要跟著行程的id
            $b->GuoupData = $query2->filter(function ($item) use ($b) {
                return $item->tripid == $b->tripid;
            });
            $b->ItineraryTag = $query3->filter(function ($item) use ($b) {
                return $item->tripid == $b->tripid;
            });;

            $collect->push($b);
        }

        return Response::json($collect, 200, $headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
/*class TourGroup
{

    public $tourGroupId ;
    public $startDate;
    public $endDate;
    public $totalPeople;
    public $reserve;
    public $nowPrice;
    public $tripScore;

}*/

class Itinerary
{
    /**
     * 建立 itinerary 的類別
     *
     * @
     */
    public $tripid;
    public $tripName;
    public $totalDay;
    public $oriPrice;
    public $description;
    public $agencyName;
    public $GuoupData;
}
/*class Itinerary_tag
{

    public $tagName ;
    public $tagDescription;
    public $tagCategory;

}*/
