# Result Card PDF Upload Feature

## Overview
Existing result card view (`/admin/results/{id}/annual-pdf`) ke saath ek **Upload PDF** button add karna hai.
- Button tabhi active hoga jab result **FINAL** ho
- Click karne par PDF generate hogi (DomPDF)
- PDF S3 me save hogi: `result-cards/{school_id}/{student_id}/result_card.pdf`
- Upload ke baad us PDF ka **view link** button ke saath dikhega

---

## STEP 1 — Explore existing code

```bash
# Find student list blade
grep -rl "View Result\|annual-pdf\|student-list" resources/views --include="*.blade.php"

# Find results controller
grep -rl "annual-pdf\|annualPdf\|student.*list" app/Http/Controllers --include="*.php"

# See routes for results
grep -n "results\|annual" routes/web.php | head -40

# Check DomPDF
cat composer.json | grep -i "dompdf\|pdf"

# Check Result model fields
grep -n "fillable\|finalize\|is_final\|status\|pdf_path" app/Models/Result*.php

# Check migrations for results table
grep -rn "finalize\|is_final\|pdf_path" database/migrations/ | grep -i result

# Check S3 config
grep -n "AWS\|S3\|s3\|FILESYSTEM" .env | sed 's/=.*/=***/'

# See existing annual-pdf blade file
cat resources/views/admin/results/annual-pdf.blade.php   # adjust path if different
```

**Show me output of all above before proceeding.**

---

## STEP 2 — Check/add `pdf_path` column to results table

```bash
grep -rn "pdf_path" database/migrations/
```

**If NOT found**, create migration:

```bash
php artisan make:migration add_pdf_path_to_results_table
```

Add to migration file:
```php
public function up()
{
    Schema::table('results', function (Blueprint $table) {
        $table->string('pdf_path')->nullable()->after('remarks');
    });
}

public function down()
{
    Schema::table('results', function (Blueprint $table) {
        $table->dropColumn('pdf_path');
    });
}
```

```bash
php artisan migrate
```

Also add `pdf_path` to Result model's `$fillable` array.

---

## STEP 3 — Install DomPDF (if not already installed)

```bash
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

If already installed, skip.

---

## STEP 4 — Add route

In `routes/web.php`, inside the admin results route group, add:

```php
Route::post('/results/{id}/upload-pdf', [ResultController::class, 'uploadResultPDF'])->name('results.upload-pdf');
```

> Use the exact controller class that handles existing result routes (found in Step 1).

---

## STEP 5 — Add `uploadResultPDF` method to ResultController

First look at the existing `annualPdf` (or similar) method to understand what variables are passed to the blade view. Then add this method right after it:

```php
public function uploadResultPDF($id)
{
    try {
        // Re-use same data loading as annualPdf() method
        // IMPORTANT: Copy the exact same $data / variable loading from annualPdf() here
        // Then generate PDF from same blade view

        $result = Result::with([/* same relations as annualPdf */])->findOrFail($id);

        // Adjust finalization check based on what field is used (is_finalized / status / etc.)
        // Check from Step 1 grep output what field name is used
        if (!$result->is_finalized) {
            return response()->json([
                'success' => false,
                'message' => 'Result is not finalized yet.'
            ], 422);
        }

        // Generate PDF — use same blade as annualPdf()
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.results.annual-pdf', [
            // Pass same variables as annualPdf() method passes to view
        ]);
        $pdf->setPaper('A4', 'landscape');

        // S3 path
        $schoolId = $result->school_id ?? ($result->student->school_id ?? 'default');
        $studentId = $result->student_id ?? $result->student->id;
        $s3Path = "result-cards/{$schoolId}/{$studentId}/result_card.pdf";

        // Save to S3
        \Storage::disk('s3')->put($s3Path, $pdf->output(), 'public');

        // Generate public URL
        $pdfUrl = \Storage::disk('s3')->url($s3Path);

        // Save path in DB
        $result->update(['pdf_path' => $s3Path]);

        return response()->json([
            'success' => true,
            'message' => 'PDF uploaded successfully.',
            'pdf_url'  => $pdfUrl
        ]);

    } catch (\Exception $e) {
        \Log::error('PDF Upload Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
```

> **Key**: Copy the exact variable loading from `annualPdf()`. Do NOT re-guess variable names.

---

## STEP 6 — Modify student list blade

Find the student list blade (from Step 1). Locate the "View Result" button — it likely looks like:

```html
@if($student->result && $student->result->is_finalized)
    <a href="..." class="btn btn-primary btn-sm">View Result</a>
@else
    <button class="btn btn-warning btn-sm">Enter Result</button>
@endif
```

**Add Upload PDF button right after View Result button**, inside the same `@if(is_finalized)` block:

```html
@if($student->result && $student->result->is_finalized)
    {{-- Existing View Result button --}}
    <a href="{{ route('results.annual-pdf', $student->result->id) }}"
       target="_blank"
       class="btn btn-primary btn-sm">
        📄 View Result
    </a>

    {{-- NEW: Upload PDF button --}}
    <button
        id="upload-btn-{{ $student->result->id }}"
        onclick="uploadResultPDF({{ $student->result->id }})"
        class="btn btn-success btn-sm ms-1">
        ☁️ Upload PDF
    </button>

    {{-- Show PDF link if already uploaded --}}
    @if($student->result->pdf_path)
        <a href="{{ \Storage::disk('s3')->url($student->result->pdf_path) }}"
           target="_blank"
           id="pdf-link-{{ $student->result->id }}"
           class="btn btn-outline-info btn-sm ms-1">
            🔗 View PDF
        </a>
    @else
        <span id="pdf-link-{{ $student->result->id }}" style="display:none;">
            <a href="#" target="_blank" class="btn btn-outline-info btn-sm ms-1">🔗 View PDF</a>
        </span>
    @endif

@else
    <button class="btn btn-warning btn-sm">Enter Result</button>
@endif
```

> Adjust the `is_finalized` condition and route name to match what's actually in the existing blade (from Step 1 output).

---

## STEP 7 — Add JavaScript to student list blade

Add this script at the bottom of the student list blade, before `@endsection`:

```html
<script>
function uploadResultPDF(resultId) {
    const btn = document.getElementById('upload-btn-' + resultId);
    const linkSpan = document.getElementById('pdf-link-' + resultId);

    btn.disabled = true;
    btn.innerHTML = '⏳ Uploading...';

    fetch('/admin/results/' + resultId + '/upload-pdf', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '✅ Uploaded';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');

            // Show/update the View PDF link
            if (linkSpan) {
                linkSpan.style.display = '';
                const anchor = linkSpan.querySelector('a') || linkSpan;
                if (anchor.tagName === 'A') {
                    anchor.href = data.pdf_url;
                } else {
                    // If it was already a visible link, update its href
                    linkSpan.href = data.pdf_url;
                }
            }
        } else {
            btn.disabled = false;
            btn.innerHTML = '☁️ Upload PDF';
            alert('Error: ' + (data.message || 'Upload failed'));
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '☁️ Upload PDF';
        alert('Network error. Please try again.');
    });
}
</script>
```

---

## STEP 8 — Test

1. Go to `/admin/results/student-list?class=6TH`
2. FINAL badge wale kisi student ke liye **Upload PDF** button click karo
3. S3 me file check karo: `result-cards/{school_id}/{student_id}/result_card.pdf`
4. **View PDF** link appear hona chahiye — click karo aur PDF verify karo
5. Database me `results.pdf_path` column me path save hona chahiye

```bash
# Verify DB
php artisan tinker
>>> App\Models\Result::where('pdf_path', '!=', null)->first(['id','pdf_path']);
```

---

## IMPORTANT NOTES

1. **Field names** — `annualPdf()` method me jo exact variables blade ko pass hote hain, wahi `uploadResultPDF()` me bhi pass karo. Step 1 me blade file dekhne ke baad adjust karo.

2. **Finalization field** — `is_finalized` ya `status = 'FINAL'` — jo bhi Step 1 ke grep me mile, wahi use karo.

3. **S3 disk** — `.env` me `AWS_BUCKET`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` properly set hone chahiye. `config/filesystems.php` me `s3` disk configured honi chahiye.

4. **`pdf_path` in `$fillable`** — Result model me add karna mat bhoolna.

5. **Logo for PDF header** — Existing `annual-pdf.blade.php` me header ka HTML already hai — same use karo, naya mat banao.
