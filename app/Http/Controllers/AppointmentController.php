<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as HttpRequest;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin =  $user->hasAnyRole(['Admin']);

        // $query = Appointment::with('user', 'service')
        // ->orderBy('id', 'desc');
        // if ($request->filled('from_date') && $request->filled('to_date')) {
        //     $query->whereBetween('requestDate', [$request->from_date, $request->to_date]);
        // }
        // $appointments = $query->paginate(6);
        if ($user->hasRole('Admin')) {
            $appointments = Appointment::query()
                ->with(['user', 'service'])
                ->orderBy('created_at', 'desc')
                ->when(HttpRequest::input('search'), function ($query, $search) {
                    $query->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('service', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('name', 'like', '%' . $search . '%');
                    });
                });

            if (HttpRequest::filled('from_date') && HttpRequest::filled('to_date')) {
                $appointments->whereBetween('date', [HttpRequest::input('from_date'), HttpRequest::input('to_date')]);
            }

            $appointments = $appointments->paginate(8)->withQueryString();
        }
         elseif ($user->hasRole('Standard')) {
            $appointments = Appointment::with(['user', 'service'])
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }) ->orderBy('created_at', request('sort', 'desc'))
                ->when(HttpRequest::input('search'), function($query, $search){
                    $query->whereHas('user', function($userQuery) use ($search){
                        $userQuery->where('name',  'like' , '%' . $search . '%');
                    })->orWhereHas('service', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('name', 'like', '%' . $search . '%');
                    });
                });
                if (HttpRequest::filled('from_date') && HttpRequest::filled('to_date')) {
                    $appointments->whereBetween('date', [HttpRequest::input('from_date'), HttpRequest::input('to_date')]);
                }

                $appointments = $appointments->paginate(8)->withQueryString();

        }  else {
            // Handle other roles or unauthorized access as needed
            return abort(403);
        }


        return inertia('Appointment/Index', [
            'appointments' => $appointments,

            'filters' => HttpRequest::only(['search']),
            'isAdmin' => $isAdmin
        ]);
    }

    public function create(){
        $user = Auth::user();
        $services = Service::all();
        $isAdmin =  $user->hasAnyRole(['Admin']);
        $isStandard =  $user->hasRole('Standard');
        // $users = User::where('type', "Standard")->get();
        $users = User::where('type', 'Standard')->get();

        return inertia('Appointment/Create', [
            'services' => $services,
            'isAdmin' => $isAdmin,
            'isStandard'=> $isStandard,
            'users' => $users,
            'user'=> $user
        ]);
    }

    public function store(Request $request){
        $user = Auth::user();

        $fields = $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'reason' => 'required',
        ]);

        if ($user->hasRole('Standard')) {
            $fields['user_id'] = $user->id;
        } elseif ($user->hasRole('Admin')) {
            // If the user is an Admin, expect the doc_id from the form
            $fields['user_id'] = $request->input('user_id');
            $fields['status'] = "Accepted";
        }


       $appointment =  Appointment::create($fields);
    //    $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " created an appointment with ". $appointment->patient->firstname. " " . $appointment->patient->lastname. " with the id# ". $appointment->id;
    //    event(new UserLog($log_entry));

        return redirect()->route('appointment.index')->with('success', 'Appointment created successfully.');
    }
    public function calendar(){
        // $appointments = Appointment::with('patient', 'service', 'doctor.user')->orderBy('date', 'asc')->get();
        $appointments = Appointment::with('user', 'service')
        ->orderBy('created_at', 'desc')
        ->get();


        return response()->json($appointments);
    }

    public function accept(Appointment $appointment){
        $appointment->update(['status' => 'Accepted']);

        $user = $appointment->user;



        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " accepted an appointment with ". $appointment->patient->firstname. " " . $appointment->patient->lastname. " with the id# ". $appointment->id;
        // event(new UserLog($log_entry));

        return redirect()->route('appointment.index')->with('success', 'Appointment approved successfully.');
    }

    public function cancel(Appointment $appointment){
        // Update the appointment status to 'canceled'
        $appointment->update(['status' => 'Cancelled']);

        $user = $appointment->user;



        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " cancelled an appointment with ". $appointment->patient->firstname. " " . $appointment->patient->lastname. " with the id# ". $appointment->id;
        // event(new UserLog($log_entry));

        return redirect()->route('appointment.index')->with('success', 'Appointment canceled successfully.');
    }

    public function withservice(Service $service = null){
        $user = Auth::user();

        $isAdmin =  $user->hasAnyRole(['Admin']);
        $isStandard =  $user->hasRole('Standard');
        $users = User::where('type', 'Standard')->get();

        if ($service) {
            $services = [$service];
        } else {
            $services = Service::all();
        }

        return inertia('Appointment/Create2', [
            'service' => $services,
            'isAdmin' => $isAdmin,
            'isStandard'=> $isStandard,
            'users' => $users,
            'user'=> $user
        ]);
    }

    public function storeService(Request $request){
        $user = Auth::user();

        $fields = $request->validate([

            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'reason' => 'required',
        ]);

        if ($user->hasRole('Standard')) {
            $fields['user_id'] = $user->id;
        } elseif ($user->hasRole('Admin')) {

            $fields['user_id'] = $request->input('user_id');
            $fields['status'] = "Accepted";
        }


        if ($request->has('service_id')) {
            $fields['service_id'] = $request->input('service_id');
            $service = Service::find($fields['service_id']);
            $successMessage = 'Service booked successfully.';
        } else {
            // No need to fetch services again, as it was done in the withservice method
            $successMessage = 'All Services retrieved successfully.';
        }


       $appointment =  Appointment::create($fields);


        return redirect()->route('appointment.index')->with('success', 'Appointment created successfully.');
    }
}

