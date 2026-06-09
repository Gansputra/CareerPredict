<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\JobListing;
use App\Models\JobCategory;
use App\Models\Skill;
use App\Models\Interest;
use Smalot\PdfParser\Parser;

class CvAnalyzerController extends Controller
{
    /**
     * Known skill keywords mapped to broader categories for CF matching.
     */
    private array $skillKeywords = [
        // Programming Languages
        'php' => 'PHP', 'laravel' => 'Laravel', 'javascript' => 'JavaScript',
        'typescript' => 'JavaScript', 'python' => 'Python', 'java' => 'Java',
        'c++' => 'C++', 'c#' => 'C#', 'ruby' => 'Ruby', 'go' => 'Go',
        'golang' => 'Go', 'rust' => 'Rust', 'swift' => 'Swift',
        'kotlin' => 'Kotlin', 'dart' => 'Dart', 'scala' => 'Scala',
        'r programming' => 'R',

        // Frontend
        'react' => 'React', 'reactjs' => 'React', 'react.js' => 'React',
        'vue' => 'Vue.js', 'vuejs' => 'Vue.js', 'angular' => 'Angular',
        'next.js' => 'Next.js', 'nextjs' => 'Next.js', 'nuxt' => 'Nuxt.js',
        'tailwind' => 'Tailwind CSS', 'bootstrap' => 'Bootstrap',
        'html' => 'HTML/CSS', 'css' => 'HTML/CSS', 'sass' => 'HTML/CSS',

        // Backend / DevOps
        'node' => 'Node.js', 'nodejs' => 'Node.js', 'express' => 'Node.js',
        'django' => 'Django', 'flask' => 'Flask', 'spring' => 'Spring',
        'docker' => 'Docker', 'kubernetes' => 'Kubernetes', 'k8s' => 'Kubernetes',
        'aws' => 'AWS', 'azure' => 'Azure', 'gcp' => 'Google Cloud',
        'ci/cd' => 'CI/CD', 'jenkins' => 'CI/CD', 'terraform' => 'Terraform',
        'linux' => 'Linux', 'nginx' => 'Linux', 'apache' => 'Linux',

        // Data & AI
        'sql' => 'SQL', 'mysql' => 'SQL', 'postgresql' => 'SQL',
        'mongodb' => 'MongoDB', 'redis' => 'Redis', 'elasticsearch' => 'Elasticsearch',
        'machine learning' => 'Machine Learning', 'deep learning' => 'Deep Learning',
        'tensorflow' => 'Machine Learning', 'pytorch' => 'Machine Learning',
        'pandas' => 'Data Analysis', 'numpy' => 'Data Analysis',
        'data analysis' => 'Data Analysis', 'data science' => 'Data Science',
        'power bi' => 'Data Analysis', 'tableau' => 'Data Analysis',

        // Design
        'figma' => 'UI Design', 'sketch' => 'UI Design', 'adobe xd' => 'UI Design',
        'photoshop' => 'Graphic Design', 'illustrator' => 'Graphic Design',
        'ui design' => 'UI Design', 'ux design' => 'UX Research',
        'ui/ux' => 'UI Design', 'user experience' => 'UX Research',
        'graphic design' => 'Graphic Design',

        // Soft Skills
        'leadership' => 'Leadership', 'team lead' => 'Leadership',
        'project management' => 'Project Management', 'agile' => 'Project Management',
        'scrum' => 'Project Management', 'jira' => 'Project Management',
        'communication' => 'Communication', 'public speaking' => 'Public Speaking',
        'problem solving' => 'Problem Solving', 'critical thinking' => 'Critical Thinking',
        'teamwork' => 'Teamwork',

        // Marketing & Business
        'seo' => 'SEO', 'sem' => 'SEO', 'google analytics' => 'SEO',
        'digital marketing' => 'Digital Marketing', 'social media' => 'Digital Marketing',
        'copywriting' => 'Copywriting', 'content writing' => 'Copywriting',
        'sales' => 'Sales', 'crm' => 'Sales', 'hubspot' => 'Sales',
        'financial' => 'Financial Literacy', 'accounting' => 'Financial Literacy',
        'excel' => 'Data Analysis',

        // Mobile
        'flutter' => 'Flutter', 'react native' => 'React Native',
        'ios' => 'iOS Development', 'android' => 'Android Development',

        // Other
        'git' => 'Git', 'github' => 'Git', 'gitlab' => 'Git',
        'api' => 'API Development', 'rest' => 'API Development', 'graphql' => 'GraphQL',
    ];

    /**
     * Interest detection keywords
     */
    private array $interestKeywords = [
        'artificial intelligence' => 'Artificial Intelligence',
        'machine learning' => 'Artificial Intelligence',
        'ai' => 'Artificial Intelligence',
        'deep learning' => 'Artificial Intelligence',
        'web development' => 'Web Development',
        'web application' => 'Web Development',
        'full stack' => 'Web Development',
        'fullstack' => 'Web Development',
        'frontend' => 'Web Development',
        'backend' => 'Web Development',
        'mobile app' => 'Mobile Apps',
        'mobile development' => 'Mobile Apps',
        'android' => 'Mobile Apps',
        'ios' => 'Mobile Apps',
        'data science' => 'Data Science',
        'data engineer' => 'Data Science',
        'big data' => 'Data Science',
        'analytics' => 'Data Science',
        'digital marketing' => 'Digital Marketing',
        'seo' => 'Digital Marketing',
        'social media' => 'Digital Marketing',
        'content marketing' => 'Digital Marketing',
        'entrepreneur' => 'Entrepreneurship',
        'startup' => 'Entrepreneurship',
        'business development' => 'Entrepreneurship',
        'creative writing' => 'Creative Writing',
        'content writer' => 'Creative Writing',
        'copywriter' => 'Creative Writing',
        'sustainability' => 'Environmental Sustainability',
        'environment' => 'Environmental Sustainability',
        'green energy' => 'Environmental Sustainability',
        'social work' => 'Social Work',
        'community' => 'Social Work',
        'ngo' => 'Social Work',
        'nonprofit' => 'Social Work',
        'finance' => 'Finance & Investment',
        'investment' => 'Finance & Investment',
        'banking' => 'Finance & Investment',
        'fintech' => 'Finance & Investment',
    ];

    public function index()
    {
        $user = Auth::user();
        $hasCvSkills = $user->skills()->wherePivot('source', 'cv')->exists();
        return view('cv.index', compact('hasCvSkills'));
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'cv_file' => 'required|mimes:pdf|max:10240',
        ], [
            'cv_file.required' => 'Please upload your CV/Resume file.',
            'cv_file.mimes' => 'Only PDF files are supported.',
            'cv_file.max' => 'File size must be less than 10MB.',
        ]);

        try {
            // 1. Parse PDF
            $parser = new Parser();
            $pdf = $parser->parseFile($request->file('cv_file')->getRealPath());
            $rawText = $pdf->getText();
            $textLower = strtolower($rawText);

            if (strlen(trim($rawText)) < 50) {
                return back()->with('error', 'Could not extract enough text from the PDF. Make sure it is not a scanned image.');
            }

            // 2. Extract Skills
            $detectedSkills = $this->extractSkills($textLower);

            // 3. Extract Interests
            $detectedInterests = $this->extractInterests($textLower);

            // 4. Extract basic info
            $cvInfo = $this->extractBasicInfo($rawText);

            // 5. Save to DB
            $this->saveToProfile($detectedSkills, $detectedInterests);

            // 6. Run Certainty Factor matching against jobs
            $recommendations = $this->calculateRecommendations($detectedSkills, $detectedInterests, $textLower);

            // 7. Determine top career categories
            $careerCategories = $this->analyzeCareerFit($detectedSkills, $detectedInterests);

            return view('cv.results', compact(
                'detectedSkills',
                'detectedInterests',
                'cvInfo',
                'recommendations',
                'careerCategories',
                'rawText'
            ));

        } catch (\Exception $e) {
            Log::error('CV Analysis failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to analyze your CV. Please ensure the file is a valid PDF and try again.');
        }
    }

    public function reset()
    {
        $user = Auth::user();
        // Only remove CV-sourced skills
        $user->skills()->wherePivot('source', 'cv')->detach();
        $user->interests()->detach();

        // Reset AI career prediction on profile
        if ($user->profile) {
            $user->profile->update([
                'cv_career_category' => null,
                'cv_career_confidence' => null,
            ]);
        }

        return redirect()->route('cv.index')
            ->with('success', 'Data CV Anda telah di-reset. Silakan unggah CV baru untuk menganalisis kembali.');
    }

    public function saveCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'confidence' => 'required|numeric|min:0|max:100',
        ]);

        $user = Auth::user();

        // Save classification category and confidence to profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'cv_career_category' => $request->category,
                'cv_career_confidence' => $request->confidence,
            ]
        );

        return response()->json(['success' => true]);
    }

    private function saveToProfile(array $skills, array $interests): void
    {
        $user = Auth::user();

        // Remove previous CV-sourced skills
        $user->skills()->wherePivot('source', 'cv')->detach();

        // Save detected skills
        foreach ($skills as $skill) {
            $dbSkill = Skill::firstOrCreate(
                ['name' => $skill['name']],
                ['type' => $this->guessSkillType($skill['name'])]
            );

            // Convert confidence (0-1) to level (1-5)
            $level = max(1, min(5, round($skill['confidence'] * 5)));

            $user->skills()->attach($dbSkill->id, [
                'level' => $level,
                'source' => 'cv',
            ]);
        }

        // Save detected interests
        $user->interests()->detach();
        foreach ($interests as $interest) {
            $dbInterest = Interest::firstOrCreate(['name' => $interest['name']]);
            $user->interests()->attach($dbInterest->id);
        }
    }

    private function guessSkillType(string $name): string
    {
        $softSkills = ['Leadership', 'Communication', 'Public Speaking', 'Problem Solving', 'Critical Thinking', 'Teamwork', 'Project Management'];
        return in_array($name, $softSkills) ? 'soft' : 'technical';
    }

    private function extractSkills(string $text): array
    {
        $found = [];

        foreach ($this->skillKeywords as $keyword => $skillName) {
            if (str_contains($text, strtolower($keyword))) {
                if (!isset($found[$skillName])) {
                    $found[$skillName] = [
                        'name' => $skillName,
                        'confidence' => 0,
                        'mentions' => 0,
                    ];
                }
                $found[$skillName]['mentions']++;
                // More mentions = higher confidence (max 0.95)
                $found[$skillName]['confidence'] = min(0.95, 0.6 + ($found[$skillName]['mentions'] * 0.1));
            }
        }

        // Sort by confidence
        usort($found, fn($a, $b) => $b['confidence'] <=> $a['confidence']);

        return $found;
    }

    private function extractInterests(string $text): array
    {
        $found = [];

        foreach ($this->interestKeywords as $keyword => $interestName) {
            if (str_contains($text, strtolower($keyword))) {
                if (!isset($found[$interestName])) {
                    $found[$interestName] = [
                        'name' => $interestName,
                        'relevance' => 0,
                    ];
                }
                $found[$interestName]['relevance']++;
            }
        }

        usort($found, fn($a, $b) => $b['relevance'] <=> $a['relevance']);

        return $found;
    }

    private function extractBasicInfo(string $text): array
    {
        $info = [
            'email' => null,
            'phone' => null,
            'word_count' => str_word_count($text),
        ];

        // Extract email
        if (preg_match('/[\w.+-]+@[\w-]+\.[\w.]+/', $text, $matches)) {
            $info['email'] = $matches[0];
        }

        // Extract phone
        if (preg_match('/(\+?\d[\d\-\s()]{7,}\d)/', $text, $matches)) {
            $info['phone'] = trim($matches[1]);
        }

        return $info;
    }

    private function calculateRecommendations(array $skills, array $interests, string $cvText): array
    {
        $jobs = JobListing::with('category')->active()->get();
        $recommendations = [];

        foreach ($jobs as $job) {
            $cfTotal = 0;
            $evidenceFound = false;
            $matchReasons = [];
            $searchable = strtolower($job->title . ' ' . $job->description . ' ' . $job->requirements);

            // 1. Skill matching (CF Expert = 0.85)
            foreach ($skills as $skill) {
                $skillLower = strtolower($skill['name']);
                if (str_contains($searchable, $skillLower)) {
                    $cfUser = $skill['confidence'];
                    $cfExpert = 0.85;
                    $cfCurrent = $cfExpert * $cfUser;
                    $cfTotal = $this->combineCF($cfTotal, $cfCurrent);
                    $matchReasons[] = $skill['name'];
                    $evidenceFound = true;
                }
            }

            // 2. Interest matching (CF Expert = 0.6)
            foreach ($interests as $interest) {
                $intLower = strtolower($interest['name']);
                if (str_contains($searchable, $intLower)) {
                    $cfUser = 0.8;
                    $cfExpert = 0.6;
                    $cfCurrent = $cfExpert * $cfUser;
                    $cfTotal = $this->combineCF($cfTotal, $cfCurrent);
                    $evidenceFound = true;
                }
            }

            // 3. Direct CV text keyword matching (CF Expert = 0.4)
            $titleWords = explode(' ', strtolower($job->title));
            $significantMatches = 0;
            foreach ($titleWords as $word) {
                if (strlen($word) > 3 && str_contains($cvText, $word)) {
                    $significantMatches++;
                }
            }
            if ($significantMatches >= 2) {
                $cfCurrent = 0.4 * min(1, $significantMatches / count($titleWords));
                $cfTotal = $this->combineCF($cfTotal, $cfCurrent);
                $evidenceFound = true;
            }

            if ($evidenceFound && $cfTotal > 0.1) {
                $recommendations[] = [
                    'job' => $job,
                    'score' => round($cfTotal, 4),
                    'confidence' => round($cfTotal * 100, 1),
                    'matched_skills' => array_slice($matchReasons, 0, 5),
                    'explanation' => $this->buildExplanation($matchReasons, $cfTotal),
                ];
            }
        }

        usort($recommendations, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($recommendations, 0, 12);
    }

    private function combineCF(float $cf1, float $cf2): float
    {
        if ($cf1 >= 0 && $cf2 >= 0) {
            return $cf1 + $cf2 * (1 - $cf1);
        } elseif ($cf1 < 0 && $cf2 < 0) {
            return $cf1 + $cf2 * (1 + $cf1);
        } else {
            return ($cf1 + $cf2) / (1 - min(abs($cf1), abs($cf2)));
        }
    }

    private function buildExplanation(array $matchedSkills, float $score): string
    {
        if (empty($matchedSkills)) {
            return 'This role aligns with your general experience profile.';
        }

        $top = array_slice($matchedSkills, 0, 3);
        $prefix = $score > 0.7 ? 'Strong match' : ($score > 0.4 ? 'Good fit' : 'Potential match');

        return "$prefix — your CV highlights " . implode(', ', $top) . " which are key requirements for this role.";
    }

    private function analyzeCareerFit(array $skills, array $interests): array
    {
        $categories = [
            'Software Development' => ['PHP', 'Laravel', 'JavaScript', 'Python', 'React', 'Vue.js', 'Node.js', 'Java', 'C++', 'C#', 'Ruby', 'Go', 'Next.js', 'Angular', 'Django', 'Flask', 'Spring', 'API Development', 'Git'],
            'Data & AI' => ['Machine Learning', 'Deep Learning', 'Data Analysis', 'Data Science', 'SQL', 'MongoDB', 'Python', 'R'],
            'DevOps & Cloud' => ['Docker', 'Kubernetes', 'AWS', 'Azure', 'Google Cloud', 'CI/CD', 'Terraform', 'Linux'],
            'Design & Creative' => ['UI Design', 'UX Research', 'Graphic Design', 'Figma'],
            'Mobile Development' => ['Flutter', 'React Native', 'iOS Development', 'Android Development', 'Dart', 'Swift', 'Kotlin'],
            'Marketing & Growth' => ['SEO', 'Digital Marketing', 'Copywriting', 'Sales'],
            'Leadership & Management' => ['Leadership', 'Project Management', 'Communication', 'Public Speaking', 'Teamwork'],
        ];

        $results = [];
        $skillNames = array_column($skills, 'name');

        foreach ($categories as $catName => $catSkills) {
            $matchCount = 0;
            $totalSkills = count($catSkills);

            foreach ($catSkills as $cs) {
                if (in_array($cs, $skillNames)) {
                    $matchCount++;
                }
            }

            if ($matchCount > 0) {
                $results[] = [
                    'name' => $catName,
                    'score' => round(($matchCount / $totalSkills) * 100),
                    'matched' => $matchCount,
                    'total' => $totalSkills,
                ];
            }
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return $results;
    }
}
