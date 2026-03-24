<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\classwork;
use Carbon\Carbon;

class StudentHomeworkApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }

            $grade = $user->grade ?? null;
            if (!$grade) {
                return response()->json(['ok' => false, 'message' => 'Student grade not found'], 422);
            }

            $classworks = classwork::where('class', $grade)
                ->where(function ($q) {
                    $q->whereNull('title')
                      ->orWhereRaw('LOWER(TRIM(title)) != ?', ['topic']);
                })
                ->orderByDesc('created_at')
                ->limit(30)
                ->get();

            $items = $classworks->map(function ($cw) {
                $createdAt = null;
                $dateLabel = 'Unknown';

                if (!empty($cw->created_at)) {
                    try {
                        $parsed = Carbon::parse($cw->created_at);
                        $createdAt = $parsed->toDateTimeString();
                        $dateLabel = $parsed->format('d M Y');
                    } catch (\Throwable $e) {
                        $createdAt = (string) $cw->created_at;
                    }
                }

                return [
                    'id' => $cw->id,
                    'class' => $cw->class,
                    'subject' => $cw->subject ?? 'NA',
                    'title' => $cw->title ?? 'NA',
                    'note' => $cw->discription ?? 'NA',
                    'category' => $cw->type ?? 'Other',
                    'fileUrl' => $cw->fileUrl,
                    'fileSize' => $cw->fileSize,
                    'youtubeLink' => $cw->youtubeLink,
                    'teacherName' => $cw->name,
                    'studentReturn' => (int) ($cw->studentReturn ?? 0),
                    'createdAt' => $createdAt,
                    'dateLabel' => $dateLabel,
                ];
            });

            $dateWise = $items
                ->groupBy('dateLabel')
                ->map(function ($group, $date) {
                    return [
                        'date' => $date,
                        'items' => $group->values(),
                    ];
                })
                ->values();

            $subjectWise = $items
                ->groupBy('subject')
                ->map(function ($group, $subject) {
                    return [
                        'subject' => $subject,
                        'items' => $group->values(),
                    ];
                })
                ->values();

            return response()->json([
                'ok' => true,
                'total' => $items->count(),
                'dateWise' => $dateWise,
                'subjectWise' => $subjectWise,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Homework API failed',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
