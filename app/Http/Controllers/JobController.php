<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\JobListing;
use App\Models\JobCategory;
use Illuminate\Support\Facades\Auth;

// Canonical job-type aliases: each key maps to ALL equivalent raw values in the DB.
// When the user picks a type, the filter uses whereIn() against all aliases.

class JobController extends Controller
{
    /**
     * All known synonyms / raw DB values grouped under a canonical display label.
     * Key   = value sent by the frontend (matches $workTypes['val'] in the Blade view).
     * Value = array of raw strings that may appear in job_listings.type.
     */
    private array $typeAliases = [
        'Penuh Waktu'         => ['Penuh Waktu', 'Full-time', 'full_time'],
        'Paruh Waktu'         => ['Paruh Waktu', 'Part-time', 'part_time'],
        'Kontrak'             => ['Kontrak', 'Contract', 'contract'],
        'Jarak Jauh (Remote)' => ['Jarak Jauh (Remote)', 'Remote', 'remote', 'freelance'],
    ];

    public function index(Request $request)
    {
        $query = JobListing::with('category')->active();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('company_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $aliases = $this->typeAliases[$request->type] ?? [$request->type];
            $query->whereIn('type', $aliases);
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        $jobsCollection = $query->get();

        $sort = $request->input('sort', 'date_desc');
        switch ($sort) {
            case 'date_asc':
                $jobsCollection = $jobsCollection->sortBy('created_at');
                break;
            case 'salary_desc':
                $jobsCollection = $jobsCollection->sort(function($a, $b) {
                    $aEmpty = empty($a->salary_range) ? 1 : 0;
                    $bEmpty = empty($b->salary_range) ? 1 : 0;
                    if ($aEmpty !== $bEmpty) return $aEmpty <=> $bEmpty;
                    
                    $aVal = $this->parseSalary($a->salary_range, true);
                    $bVal = $this->parseSalary($b->salary_range, true);
                    return $bVal <=> $aVal;
                });
                break;
            case 'salary_asc':
                $jobsCollection = $jobsCollection->sort(function($a, $b) {
                    $aEmpty = empty($a->salary_range) ? 1 : 0;
                    $bEmpty = empty($b->salary_range) ? 1 : 0;
                    if ($aEmpty !== $bEmpty) return $aEmpty <=> $bEmpty;
                    
                    $aVal = $this->parseSalary($a->salary_range, false);
                    $bVal = $this->parseSalary($b->salary_range, false);
                    return $aVal <=> $bVal;
                });
                break;
            case 'company_asc':
                $jobsCollection = $jobsCollection->sortBy('company_name', SORT_NATURAL | SORT_FLAG_CASE);
                break;
            case 'company_desc':
                $jobsCollection = $jobsCollection->sortByDesc('company_name', SORT_NATURAL | SORT_FLAG_CASE);
                break;
            case 'date_desc':
            default:
                $jobsCollection = $jobsCollection->sortByDesc('created_at');
                break;
        }

        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 12;
        $currentPageResults = $jobsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $jobs = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageResults,
            $jobsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        $jobs->withQueryString();

        $categories = JobCategory::all();
        $allLocations = JobListing::active()
            ->select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location')
            ->toArray();

        $categoryLocationMap = JobListing::active()
            ->select('category_id', 'location')
            ->distinct()
            ->get()
            ->groupBy('category_id')
            ->map(function($items) {
                return $items->pluck('location')->unique()->values()->toArray();
            })
            ->toArray();

        // --- Smart Filter Suggestions ---
        $smartSuggestions = $this->buildSmartSuggestions(Auth::user(), $categories);

        return view('jobs.index', compact('jobs', 'categories', 'allLocations', 'categoryLocationMap', 'smartSuggestions'));
    }

    /**
     * AJAX endpoint: returns jobs as JSON for live filtering (no page reload).
     */
    public function search(Request $request)
    {
        $query = JobListing::with('category')->active();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('company_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $aliases = $this->typeAliases[$request->type] ?? [$request->type];
            $query->whereIn('type', $aliases);
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        $jobsCollection = $query->get();

        $sort = $request->input('sort', 'date_desc');
        switch ($sort) {
            case 'date_asc':
                $jobsCollection = $jobsCollection->sortBy('created_at');
                break;
            case 'salary_desc':
                $jobsCollection = $jobsCollection->sort(function($a, $b) {
                    $aEmpty = empty($a->salary_range) ? 1 : 0;
                    $bEmpty = empty($b->salary_range) ? 1 : 0;
                    if ($aEmpty !== $bEmpty) return $aEmpty <=> $bEmpty;
                    return $this->parseSalary($b->salary_range, true) <=> $this->parseSalary($a->salary_range, true);
                });
                break;
            case 'salary_asc':
                $jobsCollection = $jobsCollection->sort(function($a, $b) {
                    $aEmpty = empty($a->salary_range) ? 1 : 0;
                    $bEmpty = empty($b->salary_range) ? 1 : 0;
                    if ($aEmpty !== $bEmpty) return $aEmpty <=> $bEmpty;
                    return $this->parseSalary($a->salary_range, false) <=> $this->parseSalary($b->salary_range, false);
                });
                break;
            case 'company_asc':
                $jobsCollection = $jobsCollection->sortBy('company_name', SORT_NATURAL | SORT_FLAG_CASE);
                break;
            case 'company_desc':
                $jobsCollection = $jobsCollection->sortByDesc('company_name', SORT_NATURAL | SORT_FLAG_CASE);
                break;
            default:
                $jobsCollection = $jobsCollection->sortByDesc('created_at');
        }

        $perPage = 12;
        $page    = max(1, (int) $request->input('page', 1));
        $total   = $jobsCollection->count();
        $items   = $jobsCollection->slice(($page - 1) * $perPage, $perPage)->values();

        $jobs = $items->map(function ($job) {
            return [
                'id'           => $job->id,
                'title'        => $job->title,
                'slug'         => $job->slug,
                'company_name' => $job->company_name,
                'description'  => $job->description,
                'location'     => $job->location,
                'salary_range' => $job->salary_range,
                'type'         => $job->type,
                'category'     => $job->category?->name,
            ];
        });

        return response()->json([
            'jobs'       => $jobs,
            'total'      => $total,
            'per_page'   => $perPage,
            'page'       => $page,
            'last_page'  => (int) ceil($total / $perPage),
        ]);
    }

    /**
     * Build smart filter suggestions based on user's profile data.
     * Returns an array of suggestion chips with label, icon, and filter params.
     */
    private function buildSmartSuggestions($user, $categories): array
    {
        if (!$user) return [];

        $suggestions = [];

        // Map skills to relevant search keywords
        $skillToKeyword = [
            'PHP' => 'PHP', 'Laravel' => 'Laravel', 'JavaScript' => 'JavaScript',
            'Python' => 'Python', 'React' => 'React', 'Vue.js' => 'Vue',
            'Node.js' => 'Node', 'SQL' => 'SQL', 'Data Analysis' => 'Data Analyst',
            'Machine Learning' => 'Machine Learning', 'UI Design' => 'UI/UX',
            'UX Research' => 'UX Research', 'Graphic Design' => 'Desain Grafis',
            'SEO' => 'SEO', 'Digital Marketing' => 'Digital Marketing',
            'Copywriting' => 'Copywriting', 'Project Management' => 'Project Manager',
            'Leadership' => 'Manajer', 'Communication' => 'Marketing',
            'Docker' => 'DevOps', 'AWS' => 'Cloud', 'Flutter' => 'Flutter',
            'Android Development' => 'Android', 'iOS Development' => 'iOS',
        ];

        // Map interests/skills to category names (partial match)
        $skillToCategoryKeyword = [
            'PHP' => 'teknologi', 'Laravel' => 'teknologi', 'JavaScript' => 'teknologi',
            'Python' => 'teknologi', 'React' => 'teknologi', 'Machine Learning' => 'teknologi',
            'Data Analysis' => 'teknologi', 'UI Design' => 'desain', 'UX Research' => 'desain',
            'Graphic Design' => 'desain', 'SEO' => 'pemasaran', 'Digital Marketing' => 'pemasaran',
            'Copywriting' => 'pemasaran', 'Project Management' => 'manajemen',
            'Leadership' => 'manajemen', 'Financial Literacy' => 'keuangan',
            'Flutter' => 'teknologi', 'Android Development' => 'teknologi',
        ];

        $userSkills = $user->skills()->pluck('name')->toArray();
        $userInterests = $user->interests()->pluck('name')->toArray();
        $topRecommendations = $user->recommendations()->with('job.category')->orderByDesc('score')->take(3)->get();

        $addedCategories = [];
        $addedKeywords = [];

        // 1. Suggest categories based on top CF recommendations
        foreach ($topRecommendations as $rec) {
            if ($rec->job && $rec->job->category) {
                $catId = $rec->job->category_id;
                $catName = $rec->job->category->name;
                if (!in_array($catId, $addedCategories)) {
                    $suggestions[] = [
                        'type'    => 'category',
                        'label'   => $catName,
                        'icon'    => 'fa-star',
                        'color'   => 'amber',
                        'tooltip' => 'Berdasarkan hasil Tes DNA Karir kamu',
                        'params'  => ['category' => $catId],
                    ];
                    $addedCategories[] = $catId;
                }
            }
        }

        // 2. Suggest categories based on user skills
        foreach ($userSkills as $skill) {
            if (isset($skillToCategoryKeyword[$skill])) {
                $keyword = $skillToCategoryKeyword[$skill];
                $matchedCat = $categories->first(fn($c) => str_contains(strtolower($c->name), $keyword));
                if ($matchedCat && !in_array($matchedCat->id, $addedCategories)) {
                    $suggestions[] = [
                        'type'    => 'category',
                        'label'   => $matchedCat->name,
                        'icon'    => 'fa-layer-group',
                        'color'   => 'blue',
                        'tooltip' => "Cocok dengan skill {$skill} kamu",
                        'params'  => ['category' => $matchedCat->id],
                    ];
                    $addedCategories[] = $matchedCat->id;
                }
            }
        }

        // 3. Suggest keyword searches based on top skills
        $prioritySkills = array_slice($userSkills, 0, 4);
        foreach ($prioritySkills as $skill) {
            if (isset($skillToKeyword[$skill])) {
                $kw = $skillToKeyword[$skill];
                if (!in_array($kw, $addedKeywords)) {
                    $suggestions[] = [
                        'type'    => 'search',
                        'label'   => $kw,
                        'icon'    => 'fa-magnifying-glass',
                        'color'   => 'violet',
                        'tooltip' => "Cari lowongan yang butuh skill {$skill}",
                        'params'  => ['search' => $kw],
                    ];
                    $addedKeywords[] = $kw;
                }
            }
        }

        // 4. Add salary-sorted suggestion if user has skills
        if (!empty($userSkills)) {
            $suggestions[] = [
                'type'    => 'sort',
                'label'   => 'Gaji Tertinggi',
                'icon'    => 'fa-arrow-trend-up',
                'color'   => 'emerald',
                'tooltip' => 'Tampilkan lowongan dengan gaji terbaik',
                'params'  => ['sort' => 'salary_desc'],
            ];
        }

        return array_slice($suggestions, 0, 7); // max 7 chips
    }

    public function show($slug)
    {
        $job = JobListing::with('category')->where('slug', $slug)->firstOrFail();
        $relatedJobs = JobListing::where('category_id', $job->category_id)
            ->where('id', '!=', $job->id)
            ->limit(3)
            ->get();

        $user = auth()->user();
        $requiredSkills = $job->getRequiredSkills();
        
        $matchedSkills = [];
        $missingSkills = [];
        
        if ($user && !empty($requiredSkills)) {
            $userSkills = $user->skills->pluck('name')->toArray();
            
            // Skill Aliases and translations to unify matching
            $aliases = [
                'PHP' => ['php', 'node.js', 'php/node', 'laravel'],
                'Laravel' => ['php', 'laravel', 'php/node'],
                'JavaScript' => ['javascript', 'typescript', 'js', 'react/vue'],
                'React' => ['react', 'reactjs', 'react/vue'],
                'Python' => ['python', 'statistics', 'machine learning'],
                'SQL' => ['sql', 'mysql', 'postgresql', 'database'],
                'Desain UI' => ['ui design', 'figma', 'desain ui', 'ui/ux', 'ux research', 'user research', 'riset ux'],
                'Riset UX' => ['ux research', 'user research', 'riset ux', 'ui/ux', 'desain ui', 'ui design', 'figma'],
                'Analisis Data' => ['data analysis', 'analisis data', 'statistics', 'pandas', 'numpy'],
                'Manajemen Proyek' => ['project management', 'manajemen proyek', 'agile', 'scrum', 'agile/scrum'],
                'Komunikasi' => ['communication', 'komunikasi'],
                'Kepemimpinan' => ['leadership', 'kepemimpinan'],
                'Pemecahan Masalah' => ['problem solving', 'pemecahan masalah'],
                'Desain Grafis' => ['graphic design', 'desain grafis', 'photoshop', 'illustrator'],
                'SEO' => ['seo', 'sem', 'google analytics', 'digital marketing', 'pemasaran'],
                'Copywriting' => ['copywriting', 'content writing'],
                'Public Speaking' => ['public speaking', 'presentasi'],
                'Berpikir Kritis' => ['critical thinking', 'berpikir kritis'],
                'Kerja Sama Tim' => ['teamwork', 'kerja sama tim', 'kolaborasi'],
                'Literasi Keuangan' => ['financial literacy', 'accounting', 'keuangan', 'literasi keuangan'],
            ];

            // Normalize strings to lowercase for comparison
            $normalize = fn($str) => strtolower(trim($str));

            foreach ($requiredSkills as $reqName) {
                $isMatched = false;
                $normalizedReq = $normalize($reqName);

                foreach ($userSkills as $userSkill) {
                    $normalizedUser = $normalize($userSkill);

                    // Direct match
                    if ($normalizedReq === $normalizedUser) {
                        $isMatched = true;
                        break;
                    }

                    // Check if they are aliases/synonyms
                    if (isset($aliases[$userSkill])) {
                        $userAliases = array_map($normalize, $aliases[$userSkill]);
                        if (in_array($normalizedReq, $userAliases)) {
                            $isMatched = true;
                            break;
                        }
                    }
                    if (isset($aliases[$reqName])) {
                        $reqAliases = array_map($normalize, $aliases[$reqName]);
                        if (in_array($normalizedUser, $reqAliases)) {
                            $isMatched = true;
                            break;
                        }
                    }
                }

                if ($isMatched) {
                    $matchedSkills[] = $reqName;
                } else {
                    $missingSkills[] = $reqName;
                }
            }
        } else {
            $missingSkills = $requiredSkills;
        }

        $matchPercent = count($requiredSkills) > 0 
            ? round((count($matchedSkills) / count($requiredSkills)) * 100) 
            : 0;

        $hasCvOrAssessment = $user && ($user->skills()->count() > 0);

        return view('jobs.show', compact('job', 'relatedJobs', 'requiredSkills', 'matchedSkills', 'missingSkills', 'matchPercent', 'hasCvOrAssessment'));
    }

    private function parseSalary($salaryStr, $useMax = false)
    {
        if (empty($salaryStr)) {
            return 0;
        }
        
        $salaryStr = strtolower($salaryStr);
        
        if (str_contains($salaryStr, 'negotiable') || str_contains($salaryStr, 'negosiasi')) {
            return 0;
        }
        
        $parts = preg_split('/(-|to)/i', $salaryStr);
        $targetPart = $useMax && count($parts) > 1 ? $parts[1] : $parts[0];
        $targetPart = str_replace(',', '.', $targetPart);
        
        if (preg_match('/[0-9]+(?:\.[0-9]+)?/', $targetPart, $matches)) {
            $value = floatval($matches[0]);
            $isUsd = str_contains($salaryStr, '$') || str_contains($salaryStr, 'usd');
            $isHourly = str_contains($salaryStr, 'hour') || str_contains($salaryStr, 'jam') || str_contains($salaryStr, '/h');
            $isYearly = str_contains($salaryStr, 'year') || str_contains($salaryStr, 'tahun') || str_contains($salaryStr, '/yr') || ($isUsd && $value > 1000 && !str_contains($salaryStr, 'month'));
            
            if ($isUsd) {
                if (str_contains($targetPart, 'k')) {
                    $value *= 1000;
                }
                $value *= 16000; // 1 USD = 16000 IDR approx
                
                if ($isHourly) {
                    $value *= 160; // 160 hours/month
                } elseif ($isYearly) {
                    $value /= 12; // monthly
                }
            } else {
                if (str_contains($targetPart, 'jt') || str_contains($targetPart, 'juta')) {
                    $value *= 1000000;
                } elseif (str_contains($targetPart, 'k')) {
                    $value *= 1000;
                } elseif ($value < 1000) {
                    $value *= 1000000;
                }
            }
            return $value;
        }
        return 0;
    }
}
