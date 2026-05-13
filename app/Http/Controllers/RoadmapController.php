<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    /**
     * Dummy data for roadmaps.
     * In a real application, this would be fetched from the database.
     */
    private $roadmaps = [
        'data-scientist' => [
            'id' => 1,
            'title' => 'Data Scientist',
            'slug' => 'data-scientist',
            'description' => 'Master data analysis, machine learning, and statistical modeling to become a highly sought-after Data Scientist.',
            'icon' => 'fas fa-chart-pie',
            'color' => 'blue',
            'estimated_time' => '6-8 Months',
            'difficulty' => 'Advanced',
            'steps' => [
                [
                    'title' => 'Programming Fundamentals',
                    'description' => 'Learn Python programming, data structures, and basic algorithms.',
                    'duration' => '4 Weeks',
                    'skills' => ['Python', 'Logic', 'Git'],
                ],
                [
                    'title' => 'Data Analysis & Math',
                    'description' => 'Master Pandas, NumPy, and statistics (probability, distributions).',
                    'duration' => '6 Weeks',
                    'skills' => ['Pandas', 'NumPy', 'Statistics', 'SQL'],
                ],
                [
                    'title' => 'Data Visualization',
                    'description' => 'Create compelling visual stories using Matplotlib, Seaborn, and Tableau.',
                    'duration' => '3 Weeks',
                    'skills' => ['Matplotlib', 'Seaborn', 'Tableau'],
                ],
                [
                    'title' => 'Machine Learning',
                    'description' => 'Implement regression, classification, clustering, and evaluate models using Scikit-Learn.',
                    'duration' => '8 Weeks',
                    'skills' => ['Scikit-Learn', 'ML Algorithms', 'Model Evaluation'],
                ],
                [
                    'title' => 'Deep Learning & AI',
                    'description' => 'Build neural networks using TensorFlow or PyTorch for image and text processing.',
                    'duration' => '6 Weeks',
                    'skills' => ['TensorFlow', 'PyTorch', 'Neural Networks'],
                ],
                [
                    'title' => 'Portfolio Projects',
                    'description' => 'Build end-to-end data science projects and deploy them using Streamlit or Flask.',
                    'duration' => '4 Weeks',
                    'skills' => ['Deployment', 'Streamlit', 'GitHub Portfolio'],
                ]
            ]
        ],
        'software-engineer' => [
            'id' => 2,
            'title' => 'Full Stack Developer',
            'slug' => 'software-engineer',
            'description' => 'Build comprehensive web applications from the database to the user interface.',
            'icon' => 'fas fa-laptop-code',
            'color' => 'indigo',
            'estimated_time' => '6-9 Months',
            'difficulty' => 'Intermediate',
            'steps' => [
                [
                    'title' => 'Web Fundamentals',
                    'description' => 'Learn HTML, CSS, and modern JavaScript (ES6+).',
                    'duration' => '4 Weeks',
                    'skills' => ['HTML5', 'CSS3', 'JavaScript'],
                ],
                [
                    'title' => 'Frontend Frameworks',
                    'description' => 'Master a frontend framework like React.js or Vue.js along with Tailwind CSS.',
                    'duration' => '6 Weeks',
                    'skills' => ['React / Vue', 'Tailwind CSS', 'State Management'],
                ],
                [
                    'title' => 'Backend Development',
                    'description' => 'Learn server-side programming with Node.js, PHP (Laravel), or Python.',
                    'duration' => '6 Weeks',
                    'skills' => ['Node.js / Laravel', 'RESTful APIs', 'Authentication'],
                ],
                [
                    'title' => 'Database Design',
                    'description' => 'Design scalable databases using SQL (MySQL/PostgreSQL) and NoSQL (MongoDB).',
                    'duration' => '4 Weeks',
                    'skills' => ['MySQL', 'PostgreSQL', 'MongoDB', 'ORM'],
                ],
                [
                    'title' => 'DevOps & Deployment',
                    'description' => 'Learn Docker basics, CI/CD pipelines, and hosting on AWS/Vercel.',
                    'duration' => '3 Weeks',
                    'skills' => ['Docker', 'AWS', 'Vercel', 'CI/CD'],
                ]
            ]
        ],
        'ui-ux-designer' => [
            'id' => 3,
            'title' => 'UI/UX Designer',
            'slug' => 'ui-ux-designer',
            'description' => 'Design beautiful, user-centric interfaces and map out seamless digital experiences.',
            'icon' => 'fas fa-pen-nib',
            'color' => 'purple',
            'estimated_time' => '4-6 Months',
            'difficulty' => 'Beginner Friendly',
            'steps' => [
                [
                    'title' => 'Design Fundamentals',
                    'description' => 'Understand color theory, typography, spacing, and visual hierarchy.',
                    'duration' => '3 Weeks',
                    'skills' => ['Color Theory', 'Typography', 'Layout'],
                ],
                [
                    'title' => 'UX Research & Wireframing',
                    'description' => 'Learn how to conduct user research, create personas, and draw wireframes.',
                    'duration' => '4 Weeks',
                    'skills' => ['User Research', 'Wireframing', 'User Flows'],
                ],
                [
                    'title' => 'UI Design & Prototyping',
                    'description' => 'Master Figma to create high-fidelity designs and interactive prototypes.',
                    'duration' => '6 Weeks',
                    'skills' => ['Figma', 'Prototyping', 'Design Systems'],
                ],
                [
                    'title' => 'Usability Testing',
                    'description' => 'Conduct testing sessions to gather feedback and iterate on your designs.',
                    'duration' => '3 Weeks',
                    'skills' => ['A/B Testing', 'Feedback Analysis', 'Iteration'],
                ]
            ]
        ],
        'product-manager' => [
            'id' => 4,
            'title' => 'Product Manager',
            'slug' => 'product-manager',
            'description' => 'Lead cross-functional teams to build products that users love and drive business value.',
            'icon' => 'fas fa-tasks',
            'color' => 'emerald',
            'estimated_time' => '5-7 Months',
            'difficulty' => 'Intermediate',
            'steps' => [
                [
                    'title' => 'Product Strategy & Vision',
                    'description' => 'Learn how to define product vision, strategy, and create roadmaps.',
                    'duration' => '4 Weeks',
                    'skills' => ['Strategy', 'Roadmapping', 'Market Research'],
                ],
                [
                    'title' => 'Agile & Scrum Methodologies',
                    'description' => 'Master Agile frameworks, sprint planning, and writing user stories.',
                    'duration' => '4 Weeks',
                    'skills' => ['Agile', 'Scrum', 'Jira / Trello'],
                ],
                [
                    'title' => 'Data-Driven Decision Making',
                    'description' => 'Understand product metrics, KPIs, and how to use data to prioritize features.',
                    'duration' => '4 Weeks',
                    'skills' => ['Data Analysis', 'KPIs', 'Prioritization'],
                ],
                [
                    'title' => 'Communication & Leadership',
                    'description' => 'Develop soft skills to align stakeholders, engineers, and designers.',
                    'duration' => '3 Weeks',
                    'skills' => ['Leadership', 'Communication', 'Stakeholder Management'],
                ]
            ]
        ]
    ];

    /**
     * Display a listing of the roadmaps.
     */
    public function index()
    {
        $roadmaps = $this->roadmaps;
        return view('roadmap.index', compact('roadmaps'));
    }

    /**
     * Display the specified roadmap.
     */
    public function show($slug)
    {
        if (!array_key_exists($slug, $this->roadmaps)) {
            abort(404);
        }

        $roadmap = $this->roadmaps[$slug];
        return view('roadmap.show', compact('roadmap'));
    }
}
