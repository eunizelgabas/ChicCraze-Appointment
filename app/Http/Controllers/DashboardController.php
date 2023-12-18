<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
{
    $user = auth()->user();

    if ($user->hasRole('Admin')) {
        $appointments = Appointment::with('user', 'service')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Admin logic
        $currentMonthAppointments = Appointment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $lastMonthAppointments = Appointment::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        $percentageChange = $lastMonthAppointments != 0
            ? (($currentMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100
            : 100;

        $cancelledAppointmentsByMonth = Appointment::where('status', 'Cancelled')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $upcomingAppointmentsByMonth = Appointment::where('status', 'Pending')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    } elseif ($user->hasRole('Standard')) {


        $appointments = Appointment::with(['user', 'service'])
            ->whereHas('user', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })

            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $currentMonthAppointments = Appointment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('user_id', $user->id)
            ->count();

        $lastMonthAppointments = Appointment::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->where('user_id', $user->id)
            ->count();

        $percentageChange = $lastMonthAppointments != 0
            ? (($currentMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100
            : 100;

        $cancelledAppointmentsByMonth = Appointment::where('status', 'Cancelled')
            ->where('user_id', $user->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $upcomingAppointmentsByMonth = Appointment::where('status', 'Pending')
            ->where('user_id', $user->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    } else {
        // Handle other roles or unauthorized access as needed
        return abort(403);
    }

    foreach ($appointments as &$appointment) {
        $patientName = $appointment['user']['name'] ;
        $time = Carbon::parse($appointment['time']);

    if ($time === false) {
        // Handle parsing error
        // For example, log the error or set a default time
        $formattedTime = 'Invalid Time';
    } else {
        $formattedTime = $time->format('h:i A'); // Format to 12-hour time with AM/PM
    }

        $appointment['popoverLabel'] = "{$patientName} - {$formattedTime} - {$appointment['status']}";

        // Additional code to set other properties if needed
    }

    return inertia('Dashboard', [
        'user' => $user,
        'appointments' => $appointments,
        'totalAppointments' => $currentMonthAppointments,
        'percentageChange' => $percentageChange,
        'filters' => request()->only(['search']),
        'cancelledAppointmentsByMonth' => $cancelledAppointmentsByMonth,
        'upcomingAppointmentsByMonth' => $upcomingAppointmentsByMonth
    ]);
}
}
