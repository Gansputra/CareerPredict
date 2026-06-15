<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\JobListing;
use App\Models\JobCategory;

class JobController extends Controller
{
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
            $query->where('type', $request->type);
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();
        $categories = JobCategory::all();
        $locations = JobListing::active()
            ->select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        return view('jobs.index', compact('jobs', 'categories', 'locations'));
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
}
