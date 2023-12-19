<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as HttpRequest;


class ServiceController extends Controller
{
    public function index(){
        $serviceCount = Service::count();
        // $services = Service::with('doctors')->get();
        $services = Service::query()
        ->when(HttpRequest::input('search'), function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->paginate(8)
        ->withQueryString();
        return inertia('Service/Index', [
            'services' => $services,
            'serviceCount' => $serviceCount,
            'filters' => HttpRequest::only(['search']),
        ]);
    }

    public function store(Request $request) {
        $fields = $request->validate([
            'name'=>'required',
            'image'=>'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
            'description'=>'required',
        ]);

        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = time() . "." . $request->image->extension();
            $request->image->move(public_path('services/'), $fileName);
            $fields['image'] = $fileName; // Fix: Use $fields instead of $radiologicData
        }
      $service =   Service::create($fields);
        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " created a medical service - " . $service->name;
        // event(new UserLog($log_entry));
        return redirect('/service')->with('success', 'ChicCraze Service successfully created');
    }

    public function update(Request $request, Service $service){
        $fields = $request->validate([
            'name'=>'string',
            'image'=>'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
            'description'=>'string',
        ]);

        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = time() . "." . $request->image->extension();
            $request->image->move(public_path('services/'), $fileName);
            $fields['image'] = $fileName; // Fix: Use $fields instead of $radiologicData
        }
        $service->update($fields);
        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " updated a medical service - " . $service->name;
        // event(new UserLog($log_entry));
        return redirect('/service')->with('success', "ChicCraze Service successfully updated");
    }

    public function destroy(Service $service) {
        $service->delete();
        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " deleted a medical service - " . $service->name;
        // event(new UserLog($log_entry));
        return back()->with('success', "ChicCraze Service successfully deleted");
    }
}
