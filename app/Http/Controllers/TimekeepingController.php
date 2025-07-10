<?php

namespace App\Http\Controllers;
use App\TaskActivity;
use App\User;
use Illuminate\Http\Request;

class TimekeepingController extends Controller
{
    //

    public function index(Request $request)
    {
        $date_ranges = [];
        $TaskActivity = [];
        $date_from = $request->date_from;
        $date_to = $request->date_to;
            if($request->date_from)
            {
                $TaskActivity = TaskActivity::whereBetween('date',[$date_from,$date_to])->get();
                $date_ranges = $this->dateRange($date_from,$date_to);
            }
            $users = User::get();
            return view('timekeeping.index', ['activities' => $TaskActivity,
                                        'date_ranges' => $date_ranges,
                                        'date_from' => $date_from,
                                        'date_to' => $date_to,
                                        'users' => $users,
                                        ]); 
    }
    public function myTimekeeping(Request $request)
    {
        $date_ranges = [];
        $TaskActivity = [];
        $date_from = $request->date_from;
        
        $date_to = $request->date_to;
        $last_sunday = $date_from;
        $saturday = $date_to;
            if($request->date_from)
            {
                $TaskActivity = TaskActivity::whereBetween('date',[$date_from,$date_to])->get();
                $date_ranges = $this->dateRange($date_from,$date_to);
            }
            $users = User::where('id',auth()->user()->id)->get();
            return view('timekeeping.my_timekeeping', ['activities' => $TaskActivity,
                'date_ranges' => $date_ranges,
                'date_from' => $date_from,
                'date_to' => $date_to,
                'users' => $users,
                'last_sunday' => $last_sunday,
                'saturday' => $saturday,
                ]); 
    }
    public function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <= $last ) {

        $dates[] = date( $format, $current );
        $current = strtotime( $step, $current );
    }

        return $dates;
    }
}
