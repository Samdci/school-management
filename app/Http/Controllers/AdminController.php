<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Constructor - Apply middleware for authentication and authorization
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['show']);
    }

    /**
     * Display a listing of the admins with search and pagination
     */
    public function index(Request $request)
    {
        $query = Admin::with(['user.role'])
            ->whereHas('user.role', function ($q) {
                $q->where('name', 'admin');
            })
            ->whereHas('user', function ($q) {
                $q->where('id', '!=', Auth::id()); // Exclude current user
            });

        // Handle search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phonenumber', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%")
                                ->orWhere('username', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Handle status filter
        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('is_active', $request->status);
            });
        }

        $admins = $query->orderByDesc('created_at')->paginate(15);
        $roles = Role::all();

        return view('admin', compact('admins', 'roles'));
    }

    /**
     * Show the form for creating a new admin
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin', compact('roles'));
    }

    /**
     * Store a newly created admin in both users and admins tables
     */
    public function store(Request $request)
    {
        // Comprehensive validation rules
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phonenumber' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean'
        ]);

        DB::beginTransaction();
        
        try {
            // Get admin role
            $adminRole = Role::where('name', 'admin')->first();
            if (!$adminRole) {
                throw new \Exception('Admin role not found. Please create the admin role first.');
            }

            // Create user record
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $adminRole->id,
                'is_active' => $validated['is_active'] ?? true,
                'must_change_password' => $validated['must_change_password'] ?? false,
                'email_verified_at' => now()
            ]);

            // Create admin record
            Admin::create([
                'user_id' => $user->id,
                'phonenumber' => $validated['phonenumber'],
                'gender' => $validated['gender']
            ]);

            DB::commit();

            return redirect()->route('admins.index')
                ->with('success', 'Admin created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating admin: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified admin
     */
    public function show($id)
    {
        $user = User::with(['admin', 'role'])
            ->whereHas('role', function ($q) {
                $q->where('name', 'admin');
            })
            ->findOrFail($id);

        return view('admin', compact('user'));
    }

    /**
     * Show the form for editing the specified admin
     */
    public function edit($id)
    {
        $user = User::with(['admin', 'role'])
            ->whereHas('role', function ($q) {
                $q->where('name', 'admin');
            })
            ->findOrFail($id);

        $roles = Role::all();

        return view('admin', compact('user', 'roles'));
    }

    /**
     * Update the specified admin in both tables
     */
    public function update(Request $request, $id)
    {
        $user = User::with('admin')->findOrFail($id);

        // Validation rules for update
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phonenumber' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean'
        ]);

        DB::beginTransaction();
        
        try {
            // Update user record
            $userUpdateData = [
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'is_active' => $validated['is_active'] ?? $user->is_active,
                'must_change_password' => $validated['must_change_password'] ?? $user->must_change_password
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $userUpdateData['password'] = Hash::make($validated['password']);
            }

            $user->update($userUpdateData);

            // Update admin record
            if ($user->admin) {
                $user->admin->update([
                    'phonenumber' => $validated['phonenumber'],
                    'gender' => $validated['gender']
                ]);
            } else {
                // Create admin record if it doesn't exist
                Admin::create([
                    'user_id' => $user->id,
                    'phonenumber' => $validated['phonenumber'],
                    'gender' => $validated['gender']
                ]);
            }

            DB::commit();

            return redirect()->route('admins.index')
                ->with('success', 'Admin updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating admin: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified admin from both tables
     */
    public function destroy($id)
    {
        $user = User::with('admin')->findOrFail($id);

        // Prevent deletion of current user
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account!');
        }

        DB::beginTransaction();
        
        try {
            // Delete admin record first (due to foreign key constraint)
            if ($user->admin) {
                $user->admin->delete();
            }

            // Delete user record
            $user->delete();

            DB::commit();

            return redirect()->route('admins.index')
                ->with('success', 'Admin deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error deleting admin: ' . $e->getMessage());
        }
    }

    /**
     * Toggle admin active status
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Prevent deactivating current user
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'You cannot deactivate your own account!');
        }

        try {
            $user->update([
                'is_active' => !$user->is_active
            ]);

            $status = $user->is_active ? 'activated' : 'deactivated';
            
            return redirect()->back()
                ->with('success', "Admin {$status} successfully!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating admin status: ' . $e->getMessage());
        }
    }

    /**
     * Force password reset for admin
     */
    public function forcePasswordReset($id)
    {
        $user = User::findOrFail($id);

        try {
            $user->update([
                'must_change_password' => true
            ]);

            return redirect()->back()
                ->with('success', 'Password reset flag set successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error setting password reset: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions for multiple admins
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete,force_password_reset',
            'admin_ids' => 'required|array|min:1',
            'admin_ids.*' => 'exists:users,id'
        ]);

        $adminIds = array_diff($validated['admin_ids'], [Auth::id()]); // Exclude current user

        if (empty($adminIds)) {
            return redirect()->back()
                ->with('error', 'No valid admins selected for action!');
        }

        DB::beginTransaction();
        
        try {
            switch ($validated['action']) {
                case 'activate':
                    User::whereIn('id', $adminIds)->update(['is_active' => true]);
                    $message = 'Selected admins activated successfully!';
                    break;

                case 'deactivate':
                    User::whereIn('id', $adminIds)->update(['is_active' => false]);
                    $message = 'Selected admins deactivated successfully!';
                    break;

                case 'force_password_reset':
                    User::whereIn('id', $adminIds)->update(['must_change_password' => true]);
                    $message = 'Password reset flag set for selected admins!';
                    break;

                case 'delete':
                    // Delete admin records first
                    Admin::whereIn('user_id', $adminIds)->delete();
                    // Then delete user records
                    User::whereIn('id', $adminIds)->delete();
                    $message = 'Selected admins deleted successfully!';
                    break;
            }

            DB::commit();

            return redirect()->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }
}
