<?php

namespace App\Http\Controllers\Teacher;


use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Services\StudentCardGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;
use Throwable;

class StudentCardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | VERIFY CLASSROOM ACCESS
    |--------------------------------------------------------------------------
    */

    private function authorizeClassroom(
        Section $section
    ): void {

        $teacher = auth()->user();

        abort_unless(
            $teacher &&
            $section->adviser()
                ->where('teacher_id', $teacher->getKey())
                ->exists(),
            403,
            'You are not assigned to this classroom.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK ACTIVE CLASSROOM
    |--------------------------------------------------------------------------
    */

    private function isCurrent(Section $section): bool
    {
        $section->loadMissing('schoolYear');

        return (bool) $section->is_active
            && (bool) (
                $section->schoolYear?->is_active ?? false
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SESSION KEY
    |--------------------------------------------------------------------------
    */

    private function sessionKey(
        Section $section
    ): string {

        return 'student_cards_' . $section->getKey();
    }

    /*
    |--------------------------------------------------------------------------
    | GET SAVED SEMESTER FILES
    |--------------------------------------------------------------------------
    |
    | First Semester and Second Semester files are saved separately.
    |
    */

    private function storedFiles(
        Request $request,
        Section $section
    ): array {

        $saved = $request->session()->get(
            $this->sessionKey($section),
            []
        );

        // Compatibility with previous single-file sessions.
        if (is_string($saved)) {

            return [
                1 => $saved,
                2 => null,
            ];
        }

        return [
            1 => is_array($saved)
                ? ($saved[1] ?? $saved['1'] ?? null)
                : null,

            2 => is_array($saved)
                ? ($saved[2] ?? $saved['2'] ?? null)
                : null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | READ SAVED MASTER SHEETS
    |--------------------------------------------------------------------------
    */

    private function readFiles(
        array $files,
        StudentCardGenerator $generator
    ): array {

        $lists = [
            1 => [],
            2 => [],
        ];

        foreach ([1, 2] as $semester) {

            $path = $files[$semester] ?? null;

            if ($path === null || $path === '') {
                continue;
            }

            if (
                !is_string($path) ||
                !Storage::disk('local')->exists($path)
            ) {

                throw ValidationException::withMessages([
                    'master_sheets' =>
                        "Semester {$semester} Master Sheet is missing. "
                        . 'Please upload it again.',
                ]);
            }

            $lists[$semester] = $generator->readStudents(
                Storage::disk('local')->path($path),
                $semester
            );
        }

        return $lists;
    }

    /*
    |--------------------------------------------------------------------------
    | DETECT SEMESTER FROM EXCEL CELL B5
    |--------------------------------------------------------------------------
    |
    | The Master Sheet contains merged cells B5:AU5.
    | The actual value is stored in B5.
    |
    | Example:
    |
    | FIRST SEMESTER (SY: 2026-2027)
    |
    | SECOND SEMESTER (SY: 2026-2027)
    |
    | We do not rely on the Excel filename.
    |
    */

    private function detectExcelSemester(
        string $xlsxPath,
        string $filename
    ): int {

        $reader = IOFactory::createReader('Xlsx');

        $reader->setReadDataOnly(true);

        $workbook = $reader->load($xlsxPath);

        try {

            $sheet = $workbook->getActiveSheet();

            /*
            |--------------------------------------------------------------------------
            | READ CELL B5
            |--------------------------------------------------------------------------
            */

            $text = strtoupper(
                trim(
                    (string) $sheet->getCell('B5')->getValue()
                )
            );

            $text = preg_replace(
                '/\s+/u',
                ' ',
                $text
            ) ?? $text;

            /*
            |--------------------------------------------------------------------------
            | FIRST SEMESTER
            |--------------------------------------------------------------------------
            */

            if (preg_match(
                '/\bFIRST\s+SEMESTER\b/',
                $text
            )) {
                return 1;
            }

            /*
            |--------------------------------------------------------------------------
            | SECOND SEMESTER
            |--------------------------------------------------------------------------
            */

            if (preg_match(
                '/\bSECOND\s+SEMESTER\b/',
                $text
            )) {
                return 2;
            }

            /*
            |--------------------------------------------------------------------------
            | INVALID SEMESTER
            |--------------------------------------------------------------------------
            */

            throw ValidationException::withMessages([
                'master_sheets' =>
                    "{$filename}: Cell B5 must contain "
                    . 'FIRST SEMESTER or SECOND SEMESTER.',
            ]);

        } finally {

            $workbook->disconnectWorksheets();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DISPLAY STUDENT CARD GENERATOR
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request,
        Section $section,
        StudentCardGenerator $generator
    ) {

        $this->authorizeClassroom($section);

        $section->loadMissing([
            'schoolYear',
            'strand',
        ]);

        /*
        |--------------------------------------------------------------------------
        | GET SAVED FILES
        |--------------------------------------------------------------------------
        */

        $files = $this->storedFiles(
            $request,
            $section
        );

        $lists = [
            1 => [],
            2 => [],
        ];

        $students = [];

        $loadError = null;

        /*
        |--------------------------------------------------------------------------
        | READ UPLOADED SEMESTERS
        |--------------------------------------------------------------------------
        */

        try {

            $lists = $this->readFiles(
                $files,
                $generator
            );

            $students = $generator->combineStudents(
                $lists[1],
                $lists[2]
            );

        } catch (ValidationException $exception) {

            $loadError = implode(
                ' ',
                array_merge(
                    ...array_values($exception->errors())
                )
            );

        } catch (Throwable $exception) {

            report($exception);

            $loadError =
                'Could not read the saved Master Sheets. '
                . 'Please upload them again.';
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN STUDENT CARD VIEW
        |--------------------------------------------------------------------------
        */

        return view('teacher.student-cards.index', [

            'section' => $section,

            'isCurrent' => $this->isCurrent($section),

            'students' => $students,

            'counts' => [
                1 => count($lists[1]),
                2 => count($lists[2]),
            ],

            'uploaded' => [

                1 => is_string($files[1])
                    && Storage::disk('local')->exists(
                        $files[1]
                    ),

                2 => is_string($files[2])
                    && Storage::disk('local')->exists(
                        $files[2]
                    ),
            ],

            'loadError' => $loadError,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPLOAD MASTER SHEETS
    |--------------------------------------------------------------------------
    |
    | Supports:
    |
    | master_sheets[] = Multiple Excel files
    | master_sheet    = Old single-file upload
    | first_semester  = Old First Semester input
    | second_semester = Old Second Semester input
    |
    | IMPORTANT:
    |
    | Detect each semester using Excel cell B5.
    |
    */

    public function upload(
        Request $request,
        Section $section,
        StudentCardGenerator $generator
    ) {

        /*
        |--------------------------------------------------------------------------
        | VERIFY CLASSROOM
        |--------------------------------------------------------------------------
        */

        $this->authorizeClassroom($section);

        if (!$this->isCurrent($section)) {

            return back()->withErrors([
                'master_sheets' =>
                    'This classroom or school year is inactive.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE ALL SUPPORTED UPLOAD INPUTS
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'master_sheets' => [
                'nullable',
                'array',
                'max:2',
            ],

            'master_sheets.*' => [
                'required',
                'file',
                'mimes:xlsx',
                'max:12288',
            ],

            'master_sheet' => [
                'nullable',
                'file',
                'mimes:xlsx',
                'max:12288',
            ],

            'first_semester' => [
                'nullable',
                'file',
                'mimes:xlsx',
                'max:12288',
            ],

            'second_semester' => [
                'nullable',
                'file',
                'mimes:xlsx',
                'max:12288',
            ],

            'keep_previous' => [
                'nullable',
                'boolean',
            ],

        ], [

            'master_sheets.*.mimes' =>
                'Only XLSX files are accepted.',

            'master_sheets.*.max' =>
                'Each Excel file must be 12 MB or smaller.',

            'master_sheets.max' =>
                'Select at most two Master Sheets.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | COLLECT ALL UPLOADED FILES
        |--------------------------------------------------------------------------
        |
        | Do not rename Laravel request fields.
        | Collect the uploaded files directly.
        |
        */

        $incoming = [];

        /*
        |--------------------------------------------------------------------------
        | MULTIPLE FILE INPUT
        |--------------------------------------------------------------------------
        */

        $many = $request->file(
            'master_sheets',
            []
        );

        if (is_array($many)) {

            foreach ($many as $file) {

                if ($file !== null) {

                    $incoming[] = [
                        'file' => $file,
                        'expected' => null,
                    ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SUPPORT OLDER SINGLE-FILE INPUTS
        |--------------------------------------------------------------------------
        */

        foreach (
            [
                'master_sheet',
                'first_semester',
                'second_semester',
            ] as $field
        ) {

            if ($request->hasFile($field)) {

                $expected = match ($field) {
                    'first_semester' => 1,
                    'second_semester' => 2,
                    default => null,
                };

                $incoming[] = [
                    'file' => $request->file($field),
                    'expected' => $expected,
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE NUMBER OF FILES
        |--------------------------------------------------------------------------
        */

        if (count($incoming) === 0) {

            return back()->withErrors([
                'master_sheets' =>
                    'Choose at least one Excel Master Sheet (.xlsx).',
            ]);
        }

        if (count($incoming) > 2) {

            return back()->withErrors([
                'master_sheets' =>
                    'Upload at most two files: '
                    . 'one Master Sheet per semester.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GET PREVIOUSLY UPLOADED FILES
        |--------------------------------------------------------------------------
        */

        $previous = $this->storedFiles(
            $request,
            $section
        );

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT: PREVENT OLD SEMESTER GRADES
        |--------------------------------------------------------------------------
        |
        | Default: replace previous uploaded files.
        |
        | Upload First Semester only:
        | First Semester  = Uploaded
        | Second Semester = NULL
        |
        | Upload Second Semester only:
        | First Semester  = NULL
        | Second Semester = Uploaded
        |
        | Upload both:
        | First Semester  = Uploaded
        | Second Semester = Uploaded
        |
        | If keep_previous is checked, retain earlier files.
        |
        */

        $updated = $request->boolean('keep_previous')
            ? $previous
            : [
                1 => null,
                2 => null,
            ];

        /*
        |--------------------------------------------------------------------------
        | PREPARE PRIVATE STORAGE FOLDER
        |--------------------------------------------------------------------------
        */

        $folder = 'student-card-imports/'
            . $request->user()->getKey()
            . '/'
            . $section->getKey();

        $newPaths = [];

        $seenSemesters = [];

        /*
        |--------------------------------------------------------------------------
        | PROCESS UPLOADED EXCEL FILES
        |--------------------------------------------------------------------------
        */

        try {

            foreach ($incoming as $item) {

                $file = $item['file'];

                $originalName = $file->getClientOriginalName();

                /*
                |--------------------------------------------------------------------------
                | SAVE EXCEL FILE
                |--------------------------------------------------------------------------
                */

                $path = $file->store(
                    $folder,
                    'local'
                );

                if (!$path) {

                    throw new RuntimeException(
                        "Unable to save {$originalName}."
                    );
                }

                $newPaths[] = $path;

                $absolutePath = Storage::disk('local')
                    ->path($path);

                /*
                |--------------------------------------------------------------------------
                | DETECT SEMESTER FROM EXCEL B5
                |--------------------------------------------------------------------------
                */

                $semester = $this->detectExcelSemester(
                    $absolutePath,
                    $originalName
                );

                /*
                |--------------------------------------------------------------------------
                | CHECK LEGACY INPUT SEMESTER
                |--------------------------------------------------------------------------
                */

                if (
                    $item['expected'] !== null &&
                    $item['expected'] !== $semester
                ) {

                    throw ValidationException::withMessages([
                        'master_sheets' =>
                            "{$originalName}: You selected the wrong "
                            . 'semester upload field. '
                            . "Cell B5 identifies Semester {$semester}.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | PREVENT DUPLICATE SEMESTERS
                |--------------------------------------------------------------------------
                */

                if (isset($seenSemesters[$semester])) {

                    throw ValidationException::withMessages([
                        'master_sheets' =>
                            'Both uploaded files belong to '
                            . 'the same semester. '
                            . 'Choose one file per semester.',
                    ]);
                }

                $seenSemesters[$semester] = true;

                /*
                |--------------------------------------------------------------------------
                | VALIDATE SUBJECTS AND STUDENT GRADES
                |--------------------------------------------------------------------------
                */

                $generator->readStudents(
                    $absolutePath,
                    $semester
                );

                /*
                |--------------------------------------------------------------------------
                | STORE FILE IN CORRECT SEMESTER
                |--------------------------------------------------------------------------
                */

                $updated[$semester] = $path;
            }

            /*
            |--------------------------------------------------------------------------
            | READ SELECTED SEMESTERS
            |--------------------------------------------------------------------------
            */

            $lists = $this->readFiles(
                $updated,
                $generator
            );

            /*
            |--------------------------------------------------------------------------
            | MATCH STUDENTS
            |--------------------------------------------------------------------------
            */

            $students = $generator->combineStudents(
                $lists[1],
                $lists[2]
            );

            if (!$students) {

                throw ValidationException::withMessages([
                    'master_sheets' =>
                        'No students were found in the uploaded files.',
                ]);
            }

        } catch (Throwable $exception) {

            /*
            |--------------------------------------------------------------------------
            | ROLLBACK FAILED UPLOADS
            |--------------------------------------------------------------------------
            */

            Storage::disk('local')->delete(
                $newPaths
            );

            if ($exception instanceof ValidationException) {
                throw $exception;
            }

            report($exception);

            return back()->withErrors([
                'master_sheets' =>
                    'Could not process the Master Sheets. '
                    . 'Please verify their Excel format.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE SELECTED SEMESTERS IN SESSION
        |--------------------------------------------------------------------------
        */

        $request->session()->put(
            $this->sessionKey($section),
            $updated
        );

        /*
        |--------------------------------------------------------------------------
        | DELETE REPLACED OR DESELECTED FILES
        |--------------------------------------------------------------------------
        */

        $oldPaths = array_values(
            array_filter(
                $previous,
                'is_string'
            )
        );

        $keptPaths = array_values(
            array_filter(
                $updated,
                'is_string'
            )
        );

        Storage::disk('local')->delete(
            array_diff(
                $oldPaths,
                $keptPaths
            )
        );

        /*
        |--------------------------------------------------------------------------
        | SUCCESS MESSAGE
        |--------------------------------------------------------------------------
        */

        $label = count($seenSemesters) === 2
            ? 'First and Second Semester'
            : (
                isset($seenSemesters[1])
                    ? 'First Semester'
                    : 'Second Semester'
            );

        return redirect()
            ->route(
                'teacher.classrooms.student-cards',
                $section
            )
            ->with(
                'success',
                "{$label} uploaded successfully. "
                . count($students)
                . ' students are ready.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE UPLOADED SEMESTER
    |--------------------------------------------------------------------------
    |
    | Optional method for removing a semester independently.
    |
    */

    public function removeSemester(
        Request $request,
        Section $section,
        int $semester
    ) {

        $this->authorizeClassroom($section);

        abort_unless(
            in_array($semester, [1, 2], true),
            404
        );

        if (!$this->isCurrent($section)) {

            return back()->withErrors([
                'master_sheets' =>
                    'Cannot modify an inactive classroom.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GET SAVED SEMESTER FILES
        |--------------------------------------------------------------------------
        */

        $files = $this->storedFiles(
            $request,
            $section
        );

        $oldPath = $files[$semester] ?? null;

        /*
        |--------------------------------------------------------------------------
        | CLEAR SELECTED SEMESTER
        |--------------------------------------------------------------------------
        */

        $files[$semester] = null;

        $request->session()->put(
            $this->sessionKey($section),
            $files
        );

        /*
        |--------------------------------------------------------------------------
        | DELETE SELECTED EXCEL FILE
        |--------------------------------------------------------------------------
        */

        if (is_string($oldPath)) {

            Storage::disk('local')->delete(
                $oldPath
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SUCCESS MESSAGE
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'teacher.classrooms.student-cards',
                $section
            )
            ->with(
                'success',
                ($semester === 1 ? 'First' : 'Second')
                . ' Semester Master Sheet removed.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE STUDENT CARDS
    |--------------------------------------------------------------------------
    |
    | Only saved semester files will be used.
    |
    | If First Semester only:
    | Second Semester grades remain blank.
    |
    | If Second Semester only:
    | First Semester grades remain blank.
    |
    | If both:
    | Both semester tables are filled.
    |
    */

    public function generate(
        Request $request,
        Section $section,
        StudentCardGenerator $generator
    ) {

        /*
        |--------------------------------------------------------------------------
        | VERIFY CLASSROOM
        |--------------------------------------------------------------------------
        */

        $this->authorizeClassroom($section);

        if (!$this->isCurrent($section)) {

            return $this->generationError(
                $request,
                'Cannot generate Student Cards '
                . 'for an inactive classroom.'
            );
        }

        $section->loadMissing([
            'schoolYear',
            'strand',
        ]);

        /*
        |--------------------------------------------------------------------------
        | GET SAVED SEMESTER FILES
        |--------------------------------------------------------------------------
        */

        $files = $this->storedFiles(
            $request,
            $section
        );

        if (
            empty($files[1]) &&
            empty($files[2])
        ) {

            return $this->generationError(
                $request,
                'Upload at least one Master Sheet first.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | READ STUDENTS AND GENERATE DOCUMENTS
        |--------------------------------------------------------------------------
        */

        try {

            $lists = $this->readFiles(
                $files,
                $generator
            );

            $students = $generator->combineStudents(
                $lists[1],
                $lists[2]
            );

            if (!$students) {

                throw ValidationException::withMessages([
                    'master_sheets' =>
                        'No students were found in the Master Sheets.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | IDENTIFY GRADE LEVEL AND STRAND
            |--------------------------------------------------------------------------
            */

            $grade = (string) $section->grade_level;

            $strandCode = preg_replace(
                '/[^A-Z0-9_-]/',
                '',
                strtoupper(
                    (string) ($section->strand?->code ?? '')
                )
            ) ?? '';

            /*
            |--------------------------------------------------------------------------
            | FIND WORD STUDENT CARD TEMPLATE
            |--------------------------------------------------------------------------
            */

            $templateFile = $grade
                . '-'
                . $strandCode
                . '-STUDENT-CARD.docx';

            $templatePath = storage_path(
                'app/private/templates/student-cards/'
                . $templateFile
            );

            if (
                !is_file($templatePath) ||
                !is_readable($templatePath)
            ) {

                return $this->generationError(
                    $request,
                    "Word template {$templateFile} was not found."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | PREPARE CLASSROOM INFORMATION
            |--------------------------------------------------------------------------
            */

            $classroom = [
                'year' => (string) (
                    $section->schoolYear?->name ?? ''
                ),

                'section' => (string) $section->name,

                'strand' => $strandCode,
            ];

            /*
            |--------------------------------------------------------------------------
            | GENERATE WORD STUDENT CARDS
            |--------------------------------------------------------------------------
            */

            $zipPath = $generator->makeZip(
                $students,
                $classroom,
                $templatePath
            );

            if (!is_file($zipPath)) {

                throw new RuntimeException(
                    'Student Card ZIP was not created.'
                );
            }

        } catch (ValidationException $exception) {

            return $this->generationError(
                $request,
                implode(
                    ' ',
                    array_merge(
                        ...array_values($exception->errors())
                    )
                )
            );

        } catch (Throwable $exception) {

            report($exception);

            return $this->generationError(
                $request,
                'Student Card generation failed. '
                . 'Check the Excel files and Word template.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE ZIP FILENAME
        |--------------------------------------------------------------------------
        */

        $downloadName = 'StudentCards_Grade'
            . $grade
            . '_'
            . ($strandCode ?: 'SHS')
            . '_'
            . now()->format('Ymd_His')
            . '.zip';

        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD ZIP
        |--------------------------------------------------------------------------
        */

        return response()->download(
            $zipPath,
            $downloadName,
            [
                'Content-Type' => 'application/zip',
            ]
        )->deleteFileAfterSend(true);
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATION ERROR RESPONSE
    |--------------------------------------------------------------------------
    |
    | Supports JSON responses for the Blade's fetch() loader.
    |
    */

    private function generationError(
        Request $request,
        string $message
    ) {

        if ($request->expectsJson()) {

            return response()->json([
                'message' => $message,
            ], 422);
        }

        return back()->withErrors([
            'master_sheets' => $message,
        ]);
    }
}
