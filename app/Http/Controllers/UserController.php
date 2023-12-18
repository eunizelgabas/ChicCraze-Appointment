<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as HttpRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(){

        $activeUserCount = User::count();
        $users = User::query()
        ->when(HttpRequest::input('search'), function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%') ->orWhere('email', 'like', '%' . $search . '%');
        })
        // ->where('status',1)
        ->with('roles')
        ->paginate(8)
        ->withQueryString();
        return inertia('User/Index', [
            'users' => $users,
            'activeUserCount' => $activeUserCount,
            'filters' => HttpRequest::only(['search']),
        ]);
    }
    public function create(){
        $services = Service::all();
        return inertia('User/Create', [
            'roles' => Role::all(),
            'permissions' => Permission::all(),
            'services' => $services
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'role' => 'required',
        ]);

        $data['password'] = bcrypt($data['password']); // Hash the password

        unset($data['role']);
        $type = $data['type'];
        $role = $request->role;

        $user = User::create($data);

        $user->assignRole($role);


        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " created a user account of  " . $user->firstname . " " . $user->lastname;
        // event(new UserLog($log_entry));

        return redirect()->route('user.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user){
        // $user = User::with('roles')->find($user->id);
        $roles = Role::all();
        $user->load( 'roles')->find($user->id);
        $services = Service::all();

        return inertia('User/Edit', [
            'user' => $user,
            'roles' => $roles,
            'services' => $services,
            'currentRole' => $user->roles->first()->name,
        ]);
    }

    public function update(Request $request, User $user){
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'contact_no' => 'required',
            'password' => 'nullable|string',
            'role' => 'required',
        ]);



        if (isset($data['password']) && $data['password'] !== null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']); // Remove the "password" field from the data array
        }


        $role = $request->role;
        $type = $data['type'];

        $user->update($data);

        $user->syncRoles([$data['role']]);

        $currentRole = $user->getRoleNames()->first();
        $type = $data['type'];

        if ($currentRole === 'Admin' && ($type === 'Standard')) {
            // If the user is a admin and the new type is  'doctor',
            // update the user's role to the specified role.
            $user->syncRoles([$type]);
        } else {
            // Handle other type and role changes (same as before).
            $user->syncRoles([$data['role']]);
        }
        // dd($currentRole, $type);


        // $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " updated the account of  " . $user->firstname . " " . $user->lastname;
        // event(new UserLog($log_entry));

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user) {
        $user->delete();

        return back();
        // if(!$user->doctor()->exists()) {
        //     $user->delete();
        //     $log_entry = Auth::user()->firstname . " ". Auth::user()->lastname . " deleted the account of  " . $user->firstname . " " . $user->lastname;
        //     event(new UserLog($log_entry));
        //     return back()->with('success', 'User deleted successfully.');
        // } else {
        //     return back()->with('error', 'Sorry, this user cannot be deleted because it contains existing info in the system.');
        // }
    }
}
