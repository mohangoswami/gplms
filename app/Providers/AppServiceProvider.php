<?php

namespace App\Providers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\flashNews;
use App\classwork;
use App\Term;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
     	//
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // DomPDF global options — applied to every PDF generated in the app
        Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled'         => true,
            'defaultFont'          => 'DejaVu Sans',
            'dpi'                  => 96,
            'defaultPaperSize'     => 'a4',
            'defaultPaperOrientation' => 'landscape',
        ]);

        // Lambda can load stale cached config with local absolute view paths.
        // Force runtime-correct view paths when configured paths are invalid.
        $configuredViewPaths = (array) config('view.paths', []);
        $hasValidViewPath = !empty($configuredViewPaths) && is_dir($configuredViewPaths[0]);
        if (! $hasValidViewPath) {
            $runtimeViewPath = resource_path('views');
            config(['view.paths' => [$runtimeViewPath]]);
            app('view')->getFinder()->setPaths([$runtimeViewPath]);
        }

        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // Share these only for teacher views (adjust view names as needed)
        // Keep the same view list you were using; you had some duplicates - preserve intent.
            View::composer([
                'layouts.teacher_analytics-master', // your main teacher layout (explicit)
                'teacher.*'                         // any teacher views (if you use many)
            ], function ($view) {
            // If no teacher logged in, provide empty collections (backwards compatible)
            $teacher = Auth::guard('teacher')->user();

            if (! $teacher) {
                $view->with([
                    'subCodes'   => collect(),
                    'classCodes' => collect(),
                    'classes'    => collect(),
                    'subjects'   => collect(),
                    'classworks' => collect(),
                    'flashNews'  => collect(),
                    'terms'      => collect(), // ensure terms exists in views even when no teacher
                ]);
                return;
            }

            // Use teacher helper methods (you said you added these)
            // subCodes() should return a collection of subCode models (per your Teacher model)
            $subCodes = $teacher->subCodes() ?? collect(); // collection of subCode models

            // assignedClasses() and assignedSubjects() should return collections (as you added previously)
            // Fall back to sensible defaults if helper not present
            $classes = method_exists($teacher, 'assignedClasses')
                ? $teacher->assignedClasses() ?? collect()
                : ( $subCodes->pluck('class')->unique()->values() );

            $subjects = method_exists($teacher, 'assignedSubjects')
                ? $teacher->assignedSubjects() ?? collect()
                : ( $subCodes->pluck('subject')->unique()->values() );

            // Other things
            $classworks = classwork::where('email', $teacher->email)->orderByDesc('created_at')->get();
            $flashNews  = flashNews::orderByDesc('created_at')->get();

            // Terms (make available to same teacher views)
            $terms = Term::orderBy('id')->get();

            // For backward compatibility some of your views expect 'classCodes' to be a collection of subCode models.
            // We'll set classCodes to the same collection as subCodes (this mirrors your previous behavior).
            $view->with([
                'subCodes'   => $subCodes,
                'classCodes' => $subCodes,
                'classes'    => $classes,
                'subjects'   => $subjects,
                'classworks' => $classworks,
                'flashNews'  => $flashNews,
                'terms'      => $terms,
            ]);
        });

        // NOTE: We removed the separate View::composer('layouts.teacher_analytics-master', ...) call
        // because 'terms' is already being shared above for teacher views. If you have other layouts
        // (admin layout etc.) that also need 'terms', add another composer or include that view name
        // into the array above.
    }
}
