<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class Teacher extends Authenticatable
{
    use Notifiable;


        protected $guard = 'teacher';

        protected $fillable = [
            'name', 'srNo', 'fName', 'mName', 'dob', 'doj', 'address', 'mobile', 'rfid', 'email', 'password', 'class_code0', 'class_code1', 'class_code2', 'class_code3', 'class_code4', 'class_code5', 'class_code6', 'class_code7', 'class_code8', 'class_code9', 'class_code10', 'class_code11', 'teacherImg',
        ];

        protected $hidden = [
            'password', 'remember_token',
        ];

         public function classCodes()
            {
                $ids = [];
                for ($i = 0; $i <= 11; $i++) {
                    $prop = "class_code{$i}";
                    if (!empty($this->{$prop})) {
                        $ids[] = $this->{$prop};
                    }
                }
                return collect($ids)->unique()->values();
            }


            /**
         * Return subCode models assigned to this teacher (sorted by class).
         * Cached per-teacher for a short TTL to improve repeated calls.
         *
         * @return \Illuminate\Support\Collection
         */
        public function subCodes(): \Illuminate\Support\Collection
        {
            $ids = $this->classCodes();

            if ($ids->isEmpty()) {
                return collect();
            }

            // Cache key unique per teacher and their assigned ids
            $cacheKey = "teacher_{$this->id}_subcodes_" . md5($ids->implode(','));

            // Cache for 60 seconds (adjust as needed)
            return Cache::remember($cacheKey, 60, function () use ($ids) {
                return \App\subCode::whereIn('id', $ids)->get()->sortBy('class')->values();
            });
        }

        protected static function booted()
            {
                static::saved(function ($teacher) {
                    $ids = $teacher->classCodeIds();
                    $key = "teacher_{$teacher->id}_subcodes_" . md5($ids->implode(','));
                    \Illuminate\Support\Facades\Cache::forget($key);
                });
            }
        // Classes assigned to this teacher (distinct, sorted)
        public function assignedClasses(): \Illuminate\Support\Collection
        {
            return $this->subCodes()->pluck('class')->unique()->sort()->values();
        }
        // Subjects assigned to this teacher (distinct, sorted)
        public function assignedSubjects(): \Illuminate\Support\Collection
        {
            return $this->subCodes()->pluck('subject')->unique()->sort()->values();
        }


    }
