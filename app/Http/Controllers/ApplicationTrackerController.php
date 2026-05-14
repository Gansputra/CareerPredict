<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Models\JobListing;

class ApplicationTrackerController extends Controller
{
    /**
     * Show the Kanban board for tracking job applications.
     */
    public function index()
    {
        $user = Auth::user();
        $apps = Application::with('job.category')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $statuses = [
            'wishlist' => [
                'label' => 'Wishlist',
                'icon'  => 'fas fa-star',
                'color' => 'slate',
            ],
            'applied' => [
                'label' => 'Applied',
                'icon'  => 'fas fa-paper-plane',
                'color' => 'blue',
            ],
            'interview' => [
                'label' => 'Interview',
                'icon'  => 'fas fa-comments',
                'color' => 'amber',
            ],
            'offered' => [
                'label' => 'Offered 🎉',
                'icon'  => 'fas fa-trophy',
                'color' => 'emerald',
            ],
            'rejected' => [
                'label' => 'Rejected',
                'icon'  => 'fas fa-times-circle',
                'color' => 'red',
            ],
        ];

        // Group applications by status
        $columns = [];
        foreach ($statuses as $key => $meta) {
            $columns[$key] = array_merge($meta, [
                'cards' => $apps->where('status', $key)->values(),
            ]);
        }

        return view('tracker.index', compact('columns', 'statuses'));
    }

    /**
     * Save a job to the tracker (from Job Explorer).
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:job_listings,id',
            'status' => 'sometimes|in:wishlist,applied,interview,offered,rejected',
        ]);

        $user = Auth::user();

        // Check if already tracked
        $existing = Application::where('user_id', $user->id)
            ->where('job_id', $request->job_id)
            ->first();

        if ($existing) {
            return back()->with('warning', 'This job is already in your tracker.');
        }

        Application::create([
            'user_id' => $user->id,
            'job_id'  => $request->job_id,
            'status'  => $request->status ?? 'wishlist',
            'notes'   => null,
        ]);

        return back()->with('success', 'Job saved to your tracker!');
    }

    /**
     * Update status of an application (AJAX for drag/dropdown).
     */
    public function updateStatus(Request $request, Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:wishlist,applied,interview,offered,rejected',
        ]);

        $application->update(['status' => $request->status]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'status' => $request->status]);
        }

        return back()->with('success', 'Status updated!');
    }

    /**
     * Update notes of an application.
     */
    public function updateNotes(Request $request, Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $application->update(['notes' => $request->notes]);

        return back()->with('success', 'Notes updated!');
    }

    /**
     * Remove a job from the tracker.
     */
    public function destroy(Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $application->delete();

        return back()->with('success', 'Removed from tracker.');
    }
}
