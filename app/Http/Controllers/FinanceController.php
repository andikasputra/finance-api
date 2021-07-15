<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FinanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        return response(Auth::user()->finances);
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'count' => 'required|min:1',
            'price' => 'required|min:1',
            'type' => 'required|in:income,expense',
            'date' => 'required|date'
        ]);
        $finance = new Finance();
        $finance->title = $validated['title'];
        $finance->description = $validated['description'];
        $finance->count = $validated['count'];
        $finance->price = $validated['price'];
        $finance->type = $validated['type'];
        $finance->date = date('Y-m-d', strtotime($validated['date']));
        $finance->user_id = Auth::user()->id;
        $finance->save();

        return response($finance, 201);
    }

    public function show(Request $request, $id)
    {
        $finance = Finance::where('id',$id)->where('user_id', Auth::user()->id)->first();
        if (empty($finance)) {
            abort(404, 'Data not found');
        }
        // if (Gate::denies('update-finance', $finance)) {
        //     abort(403, 'You dont have access to this data');
        // }
        $finance->load('images');
        return response($finance);
    }

    public function update(Request $request, $id)
    {
        $finance = Finance::where('id',$id)->where('user_id', Auth::user()->id)->first();
        if (empty($finance)) {
            abort(404, 'Data not found');
        }
        // if (Gate::denies('update-finance', $finance)) {
        //     abort(403, 'You dont have access to this data');
        // }
        $validated = $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'count' => 'required|min:1',
            'price' => 'required|min:1',
            'type' => 'required|in:income,expense',
            'date' => 'required|date'
        ]);
        $finance->title = $validated['title'];
        $finance->description = $validated['description'];
        $finance->count = $validated['count'];
        $finance->price = $validated['price'];
        $finance->type = $validated['type'];
        $finance->date = date('Y-m-d', strtotime($validated['date']));
        $finance->save();
        return response($finance);
    }

    public function uploadImage(Request $request, $id)
    {
        $finance = Finance::where('id',$id)->where('user_id', Auth::user()->id)->first();
        if (empty($finance)) {
            abort(404, 'Data not found');
        }
        // if (Gate::denies('update-finance', $finance)) {
        //     abort(403, 'You dont have access to this data');
        // }
        if (!$request->hasFile('image')) {
            abort(400, 'image is required');
        }
        $path = $request->file('image')->store('images');
        $image = new Image();
        $image->finance_id = $id;
        $image->path = $path;
        $image->save();
        return response($image, 201);
    }

    public function destroy(Request $request, $id)
    {
        $finance = Finance::where('id',$id)->where('user_id', Auth::user()->id)->first();
        if (empty($finance)) {
            abort(404, 'Data not found');
        }
        // if (Gate::denies('delete-finance', $finance)) {
        //     abort(403, 'You dont have access to this data');
        // }
        $finance->delete();
        return response($finance);
    }
}
