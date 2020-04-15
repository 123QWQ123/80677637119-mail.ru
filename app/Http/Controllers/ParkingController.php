<?php

namespace App\Http\Controllers;

use App\Car;
use App\Parking;
use DataTables;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Parking::with('car')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){

                    $btn = '<a style="width: 100%;" href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editParking">Edit</a>';

                    $btn = $btn.' <a style="width: 100%;" href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteParking">Delete</a>';

                    return $btn;
                })
                ->addColumn('paid', function($row){
                    return $row->paid ? 'paid' : 'no paid';
                })
                ->addColumn('color', function($row){
                    return $row->car->color ?? '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $this->validate($request, [
            'brand' => 'required',
            'model' => 'required',
            'number' => ['required', 'regex:/^((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/m'],
        ]);

        $car = Car::firstOrCreate(
            [
                'number' => $data['number'],
                'model' => $data['model'],
                'brand' => $data['brand'],
                'color' => $request->get('color')
            ]);

        Parking::updateOrCreate(['id' => $request->get('parking_id')],
            [
                'car_id' => $car->id,
                'comment' => $request->get('comment'),
                'paid' => $request->has('paid'),
            ]);

        return response()->json(['success'=>'Parking saved successfully.']);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Parking::with('car')->find($id);
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Parking::find($id)->delete();

        return response()->json(['success'=>'Parking deleted successfully.']);
    }
}
