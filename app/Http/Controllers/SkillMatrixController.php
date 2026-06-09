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

        // Real user skills from DB (from CV Analyzer / Career DNA Test)
        // Convert level (1-5) to percentage (0-100) for chart display
        $dbSkills = $user->skills()->get();
        $userSkills = [];

        foreach ($dbSkills as $skill) {
            $userSkills[$skill->name] = ($skill->pivot->level / 5) * 100;
        }

        // Also map common aliases (both English and Indonesian) so they match career requirement names
        $aliases = [
            'PHP' => 'PHP/Node', 'Laravel' => 'PHP/Node', 'Node.js' => 'PHP/Node',
            'React' => 'React/Vue', 'Vue.js' => 'React/Vue', 'Angular' => 'React/Vue',
            'HTML/CSS' => 'HTML/CSS', 'Tailwind CSS' => 'CSS', 'Bootstrap' => 'CSS',
            'UI Design' => 'Figma', 'Desain UI' => 'Figma',
            'UX Research' => 'User Research', 'Riset UX' => 'User Research',
            'Data Analysis' => 'Data Analysis', 'Analisis Data' => 'Data Analysis',
            'Data Science' => 'Statistics', 'Statistika' => 'Statistics',
            'Machine Learning' => 'Machine Learning', 'Deep Learning' => 'Deep Learning',
            'Project Management' => 'Agile/Scrum', 'Manajemen Proyek' => 'Agile/Scrum',
            'Communication' => 'Communication', 'Komunikasi' => 'Communication',
            'Leadership' => 'Leadership', 'Kepemimpinan' => 'Leadership',
            'Data Viz' => 'Data Viz', 'Visualisasi Data' => 'Data Viz',
        ];

        foreach ($dbSkills as $skill) {
            if (isset($aliases[$skill->name]) && !isset($userSkills[$aliases[$skill->name]])) {
                $userSkills[$aliases[$skill->name]] = ($skill->pivot->level / 5) * 100;
            }
        }

        return view('skillmatrix.index', compact('careers', 'userSkills', 'user'));
    }
}
