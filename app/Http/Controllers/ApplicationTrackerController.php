<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationTrackerController extends Controller
{
    /**
     * Show the Kanban board for tracking job applications.
     */
    public function index()
    {
        // Demo static data – in a real app this would come from DB per user
        $columns = [
            'wishlist' => [
                'label' => 'Wishlist',
                'icon'  => 'fas fa-star',
                'color' => 'slate',
                'cards' => [
                    ['id'=>1, 'company'=>'Tokopedia',    'role'=>'Data Scientist',      'location'=>'Jakarta', 'salary'=>'IDR 18M', 'logo'=>'T'],
                    ['id'=>2, 'company'=>'Gojek',        'role'=>'Backend Engineer',    'location'=>'Remote',  'salary'=>'IDR 15M', 'logo'=>'G'],
                ],
            ],
            'applied' => [
                'label' => 'Applied',
                'icon'  => 'fas fa-paper-plane',
                'color' => 'blue',
                'cards' => [
                    ['id'=>3, 'company'=>'Shopee',       'role'=>'Product Manager',     'location'=>'Jakarta', 'salary'=>'IDR 20M', 'logo'=>'S'],
                    ['id'=>4, 'company'=>'Traveloka',    'role'=>'UI/UX Designer',      'location'=>'Jakarta', 'salary'=>'IDR 14M', 'logo'=>'T'],
                ],
            ],
            'interview' => [
                'label' => 'Interview',
                'icon'  => 'fas fa-comments',
                'color' => 'amber',
                'cards' => [
                    ['id'=>5, 'company'=>'Bukalapak',    'role'=>'Frontend Developer',  'location'=>'Remote',  'salary'=>'IDR 12M', 'logo'=>'B'],
                ],
            ],
            'offered' => [
                'label' => 'Offered 🎉',
                'icon'  => 'fas fa-trophy',
                'color' => 'emerald',
                'cards' => [
                    ['id'=>6, 'company'=>'Ruangguru',    'role'=>'Full Stack Developer','location'=>'Jakarta', 'salary'=>'IDR 16M', 'logo'=>'R'],
                ],
            ],
            'rejected' => [
                'label' => 'Rejected',
                'icon'  => 'fas fa-times-circle',
                'color' => 'red',
                'cards' => [
                    ['id'=>7, 'company'=>'Grab',         'role'=>'DevOps Engineer',     'location'=>'Jakarta', 'salary'=>'IDR 17M', 'logo'=>'G'],
                ],
            ],
        ];

        return view('tracker.index', compact('columns'));
    }
}
