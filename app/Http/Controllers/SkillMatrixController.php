<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillMatrixController extends Controller
{
    /**
     * Show the skill matrix page.
     * Compares user's current skills vs. skills required for target careers.
     */
    public function index()
    {
        $user = Auth::user();

        // Target careers with their required skills & proficiency levels (0-100)
        $careers = [
            'data-scientist' => [
                'title'  => 'Data Scientist',
                'icon'   => 'fas fa-chart-pie',
                'color'  => 'blue',
                'skills' => [
                    'Python'          => 90,
                    'Statistics'      => 85,
                    'Machine Learning'=> 80,
                    'SQL'             => 75,
                    'Data Viz'        => 70,
                    'Deep Learning'   => 65,
                ],
            ],
            'software-engineer' => [
                'title'  => 'Full Stack Dev',
                'icon'   => 'fas fa-laptop-code',
                'color'  => 'indigo',
                'skills' => [
                    'JavaScript' => 90,
                    'HTML/CSS'   => 85,
                    'React/Vue'  => 80,
                    'PHP/Node'   => 80,
                    'SQL'        => 70,
                    'Docker'     => 60,
                ],
            ],
            'ui-ux-designer' => [
                'title'  => 'UI/UX Designer',
                'icon'   => 'fas fa-pen-nib',
                'color'  => 'purple',
                'skills' => [
                    'Figma'         => 90,
                    'User Research' => 85,
                    'Prototyping'   => 80,
                    'Typography'    => 75,
                    'Color Theory'  => 75,
                    'CSS'           => 60,
                ],
            ],
            'product-manager' => [
                'title'  => 'Product Manager',
                'icon'   => 'fas fa-tasks',
                'color'  => 'emerald',
                'skills' => [
                    'Strategy'      => 85,
                    'Agile/Scrum'   => 85,
                    'Data Analysis' => 75,
                    'Communication' => 90,
                    'Roadmapping'   => 80,
                    'Leadership'    => 80,
                ],
            ],
        ];

        // Demo: simulated user current skill levels (in a real app, from DB / assessment results)
        $userSkills = [
            'Python'          => 55,
            'Statistics'      => 40,
            'Machine Learning'=> 30,
            'SQL'             => 60,
            'Data Viz'        => 45,
            'Deep Learning'   => 20,
            'JavaScript'      => 70,
            'HTML/CSS'        => 80,
            'React/Vue'       => 50,
            'PHP/Node'        => 55,
            'Docker'          => 25,
            'Figma'           => 35,
            'User Research'   => 30,
            'Prototyping'     => 40,
            'Typography'      => 50,
            'Color Theory'    => 45,
            'CSS'             => 65,
            'Strategy'        => 40,
            'Agile/Scrum'     => 55,
            'Data Analysis'   => 50,
            'Communication'   => 75,
            'Roadmapping'     => 35,
            'Leadership'      => 45,
        ];

        return view('skillmatrix.index', compact('careers', 'userSkills', 'user'));
    }
}
