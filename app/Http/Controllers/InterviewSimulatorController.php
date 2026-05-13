<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InterviewSimulatorController extends Controller
{
    /**
     * Bank of interview questions grouped by career category.
     */
    private $questions = [
        'general' => [
            'label' => 'General / HR',
            'icon'  => 'fas fa-user-tie',
            'color' => 'slate',
            'questions' => [
                ['q' => 'Tell me about yourself.', 'tip' => 'Keep it professional – focus on your background, key skills, and why you\'re excited about this role. Use the Present–Past–Future structure.'],
                ['q' => 'What are your greatest strengths?', 'tip' => 'Pick 2–3 strengths directly relevant to the job. Back each one up with a concrete example.'],
                ['q' => 'What is your biggest weakness?', 'tip' => 'Choose a genuine weakness, but frame it as something you are actively working to improve. Avoid clichés like "I work too hard."'],
                ['q' => 'Where do you see yourself in 5 years?', 'tip' => 'Show ambition but align your goal with the company\'s growth. Mention skills you want to develop.'],
                ['q' => 'Why do you want to leave your current job?', 'tip' => 'Stay positive. Focus on growth opportunities at the new company rather than criticising your current employer.'],
                ['q' => 'Why should we hire you?', 'tip' => 'Summarise your top 3 relevant skills, give a brief result-oriented example for each, then close with your enthusiasm for the role.'],
            ],
        ],
        'data-scientist' => [
            'label' => 'Data Scientist',
            'icon'  => 'fas fa-chart-pie',
            'color' => 'blue',
            'questions' => [
                ['q' => 'Explain the difference between supervised and unsupervised learning.', 'tip' => 'Supervised learning uses labelled data; unsupervised finds patterns in unlabelled data. Give a real-world example for each (e.g., spam detection vs. customer segmentation).'],
                ['q' => 'How do you handle missing data in a dataset?', 'tip' => 'Mention multiple strategies: deletion (listwise/pairwise), mean/median imputation, model-based imputation (KNN, regression), and when each is appropriate.'],
                ['q' => 'What is overfitting and how do you prevent it?', 'tip' => 'Overfitting = model memorises training data. Prevention: cross-validation, regularisation (L1/L2), dropout, reducing model complexity, more data.'],
                ['q' => 'Explain the bias–variance tradeoff.', 'tip' => 'High bias = underfitting; high variance = overfitting. A good model balances both. Use a simple diagram analogy to explain.'],
                ['q' => 'Walk me through a data science project you completed.', 'tip' => 'Use STAR format: Situation, Task, Action, Result. Mention the tools, the metric you optimised, and the business impact.'],
            ],
        ],
        'software-engineer' => [
            'label' => 'Software Engineer',
            'icon'  => 'fas fa-laptop-code',
            'color' => 'indigo',
            'questions' => [
                ['q' => 'Explain the difference between REST and GraphQL.', 'tip' => 'REST uses fixed endpoints per resource; GraphQL uses a single endpoint where the client specifies exactly what data it needs. Mention trade-offs (over-fetching, complexity).'],
                ['q' => 'What is the SOLID principle?', 'tip' => 'S–Single Responsibility, O–Open/Closed, L–Liskov Substitution, I–Interface Segregation, D–Dependency Inversion. Give a brief example for at least two.'],
                ['q' => 'How do you ensure code quality in a team?', 'tip' => 'Mention code reviews, linting, automated testing (unit, integration, e2e), CI/CD pipelines, and documentation standards.'],
                ['q' => 'Explain how a database index works.', 'tip' => 'An index is a data structure (often B-tree) that speeds up reads at the cost of slower writes and extra storage. Mention when NOT to use indexes.'],
                ['q' => 'Describe a challenging bug you fixed.', 'tip' => 'Use STAR. Highlight your debugging process: reproduction, isolation, root-cause analysis, fix, and regression test.'],
            ],
        ],
        'ui-ux-designer' => [
            'label' => 'UI/UX Designer',
            'icon'  => 'fas fa-pen-nib',
            'color' => 'purple',
            'questions' => [
                ['q' => 'Walk me through your design process.', 'tip' => 'Describe your end-to-end process: Research → Define → Ideate → Prototype → Test → Iterate. Mention tools (Figma, Maze, etc.).'],
                ['q' => 'How do you handle design feedback you disagree with?', 'tip' => 'Show empathy, ask clarifying questions to understand the reasoning, present data to support your decision, and be willing to compromise.'],
                ['q' => 'What\'s the difference between UX and UI?', 'tip' => 'UX = the overall experience and feel; UI = the visual and interactive elements. A great UI can still have poor UX.'],
                ['q' => 'How do you make designs accessible?', 'tip' => 'Mention WCAG guidelines, colour contrast ratios (4.5:1 for normal text), keyboard navigation, screen reader support, and alt text for images.'],
                ['q' => 'Describe a time your design significantly improved a metric.', 'tip' => 'Give a concrete before/after metric (e.g., conversion rate, task completion time). Explain your research, design decision, and A/B test result.'],
            ],
        ],
        'product-manager' => [
            'label' => 'Product Manager',
            'icon'  => 'fas fa-tasks',
            'color' => 'emerald',
            'questions' => [
                ['q' => 'How do you prioritise features when everything is urgent?', 'tip' => 'Describe a framework: RICE (Reach, Impact, Confidence, Effort), MoSCoW, or Kano model. Show how you balance business value vs. effort.'],
                ['q' => 'Tell me how you define success for a product.', 'tip' => 'Connect to business goals → OKRs → key metrics (DAU, retention, NPS, revenue). Emphasise leading vs. lagging indicators.'],
                ['q' => 'How do you work with engineering when you disagree?', 'tip' => 'Show you understand technical constraints. Describe how you align on "why" before "what", use data to resolve disagreements, and maintain trust.'],
                ['q' => 'How do you gather and use customer feedback?', 'tip' => 'Mention qualitative (interviews, usability tests) and quantitative (surveys, analytics). Explain how you synthesise insights into actionable requirements.'],
                ['q' => 'Describe a product failure and what you learned.', 'tip' => 'Be honest. Focus on what indicators you missed, what you would do differently, and the growth mindset you developed.'],
            ],
        ],
    ];

    /**
     * Show the interview simulator landing page.
     */
    public function index()
    {
        $categories = $this->questions;
        // Strip questions array so we only pass meta for the index
        $categoryMeta = [];
        foreach ($categories as $key => $cat) {
            $categoryMeta[$key] = [
                'label' => $cat['label'],
                'icon'  => $cat['icon'],
                'color' => $cat['color'],
                'count' => count($cat['questions']),
            ];
        }
        return view('interview.index', compact('categoryMeta'));
    }

    /**
     * Show questions for a specific career category.
     */
    public function show($category)
    {
        if (!array_key_exists($category, $this->questions)) {
            abort(404);
        }
        $cat = $this->questions[$category];
        return view('interview.show', compact('cat', 'category'));
    }
}
