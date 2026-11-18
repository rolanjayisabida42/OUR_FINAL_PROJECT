<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Services\MasterSheetGenerator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use RuntimeException;
use Throwable;

class ClassRecordTestController extends Controller
{
    private const WORKSHEET = 'Final Semestral Grade';

    private const MAX_FILES = 11;

    /*
    |--------------------------------------------------------------------------
    | EXTRACT CLASS RECORDS AND GENERATE MASTER SHEET
    |--------------------------------------------------------------------------
    */

    public function extract(
        Request $request,
        Section $section,
        MasterSheetGenerator $generator
    ) {
        /*
        |--------------------------------------------------------------------------
        | 1. AUTHORIZE THE TEACHER
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (string) ($section->adviser?->teacher?->getKey() ?? '')
                === (string) auth()->id(),
            403,
            'You are not assigned to this classroom.'
        );

        /*
        |--------------------------------------------------------------------------
        | 2. VALIDATE UPLOADED FILES, GRADE, AND SEMESTER
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'grade_level' => [
                'required',
                'in:11,12',
            ],

            'semester' => [
                'required',
                'in:1,2',
            ],

            'class_records' => [
                'required',
                'array',
                'min:1',
                'max:' . self::MAX_FILES,
            ],

            'class_records.*' => [
                'bail',
                'required',
                'file',
                'extensions:xlsx',
                'max:15360',
            ],

        ], [

            'grade_level.required' =>
                'Please select a grade level.',

            'grade_level.in' =>
                'Grade level must be Grade 11 or Grade 12.',

            'semester.required' =>
                'Please select a semester.',

            'semester.in' =>
                'Semester must be First or Second Semester.',

            'class_records.required' =>
                'Please select at least one Class Record.',

            'class_records.min' =>
                'Upload at least one Class Record.',

            'class_records.max' =>
                'You can upload a maximum of 11 Class Records.',

            'class_records.*.extensions' =>
                'Only XLSX Excel files are allowed.',

            'class_records.*.max' =>
                'Each Excel file must not exceed 15 MB.',

        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. GET THE SELECTED GRADE AND SEMESTER
        |--------------------------------------------------------------------------
        */

        $gradeLevel = (string) $validated['grade_level'];

        $semesterNumber = (int) $validated['semester'];

        $semesterLabel = $semesterNumber === 1
            ? 'FIRST SEMESTER'
            : 'SECOND SEMESTER';

        /*
        |--------------------------------------------------------------------------
        | 4. VALIDATE CLASSROOM GRADE
        |--------------------------------------------------------------------------
        */

        $section->loadMissing([
            'schoolYear',
            'strand',
        ]);

        if ($gradeLevel !== (string) $section->grade_level) {

            throw ValidationException::withMessages([

                'grade_level' =>
                    'The selected grade level does not match '
                    . 'the current classroom.',

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. VALIDATE SCHOOL YEAR
        |--------------------------------------------------------------------------
        */

        $schoolYear = trim(
            (string) ($section->schoolYear?->name ?? '')
        );

        if ($schoolYear === '') {

            throw ValidationException::withMessages([

                'grade_level' =>
                    'This classroom has no assigned school year.',

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 6. PREPARE EXTRACTED DATA
        |--------------------------------------------------------------------------
        */

        $allRecords = [];

        $failures = [];

        /*
        |--------------------------------------------------------------------------
        | 7. PROCESS UPLOADED CLASS RECORDS
        |--------------------------------------------------------------------------
        */

        foreach ($validated['class_records'] as $index => $file) {

            $spreadsheet = null;
            $reader = null;
            $sheet = null;

            try {

                $filePath = $file->getRealPath();

                if (!$filePath) {

                    throw new RuntimeException(
                        'Unable to access uploaded file.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | 8. CREATE XLSX READER
                |--------------------------------------------------------------------------
                */

                $reader = IOFactory::createReader('Xlsx');

                if (!$reader->canRead($filePath)) {

                    throw new RuntimeException(
                        'Invalid XLSX workbook.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | 9. VERIFY WORKSHEET
                |--------------------------------------------------------------------------
                */

                $worksheetNames = $reader->listWorksheetNames(
                    $filePath
                );

                if (!in_array(
                    self::WORKSHEET,
                    $worksheetNames,
                    true
                )) {

                    throw new RuntimeException(
                        'Worksheet "' . self::WORKSHEET
                        . '" was not found.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | 10. OPTIMIZE EXCEL MEMORY
                |--------------------------------------------------------------------------
                */

                $reader->setReadDataOnly(true);

                $reader->setLoadSheetsOnly([
                    self::WORKSHEET,
                ]);

                $reader->setReadEmptyCells(false);

                $reader->setIncludeCharts(false);

                /*
                |--------------------------------------------------------------------------
                | 11. LOAD ONLY REQUIRED CELLS
                |--------------------------------------------------------------------------
                */

                $reader->setReadFilter(

                    new class implements IReadFilter {

                        public function readCell(
                            string $columnAddress,
                            int $row,
                            string $worksheetName = ''
                        ): bool {

                            $column = Coordinate::columnIndexFromString(
                                $columnAddress
                            );

                            // Semester and subject: V8:AB9
                            if ($row === 8 || $row === 9) {

                                return $column >= 22 &&
                                       $column <= 28;
                            }

                            // Teacher: I10:R10
                            if ($row === 10) {

                                return $column >= 9 &&
                                       $column <= 18;
                            }

                            // Male students
                            if ($row >= 13 && $row <= 62) {

                                return $column >= 2 &&
                                       $column <= 21;
                            }

                            // Female students
                            if ($row >= 64 && $row <= 113) {

                                return $column >= 2 &&
                                       $column <= 21;
                            }

                            return false;
                        }
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | 12. LOAD WORKSHEET
                |--------------------------------------------------------------------------
                */

                $spreadsheet = $reader->load($filePath);

                $sheet = $spreadsheet->getSheetByName(
                    self::WORKSHEET
                );

                if (!$sheet) {

                    throw new RuntimeException(
                        'Unable to load the required worksheet.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | 13. EXTRACT SEMESTER, SUBJECT, AND TEACHER
                |--------------------------------------------------------------------------
                */

                $semester = $this->readRange(
                    $sheet,
                    'V',
                    'AB',
                    8
                );

                $subject = $this->readRange(
                    $sheet,
                    'V',
                    'AB',
                    9
                );

                $teacher = $this->readRange(
                    $sheet,
                    'I',
                    'R',
                    10
                );

                /*
                |--------------------------------------------------------------------------
                | 14. VERIFY CLASS RECORD SEMESTER
                |--------------------------------------------------------------------------
                */

                $rawSemester = trim(
                    (string) ($semester['value'] ?? '')
                );

                $detectedSemester = $this->detectSemester(
                    $rawSemester
                );

                if (
                    $rawSemester !== '' &&
                    $detectedSemester === null
                ) {

                    throw new RuntimeException(
                        'Unable to recognize semester "'
                        . $rawSemester
                        . '". Check the semester heading in Excel.'
                    );
                }

                if (
                    $detectedSemester !== null &&
                    $detectedSemester !== $semesterNumber
                ) {

                    throw new RuntimeException(
                        'This Class Record belongs to '
                        . ($detectedSemester === 1
                            ? 'FIRST SEMESTER'
                            : 'SECOND SEMESTER')
                        . ', but you selected '
                        . $semesterLabel
                        . '.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | 15. EXTRACT MALE STUDENTS
                |--------------------------------------------------------------------------
                */

                $maleStudents = $this->extractStudents(
                    $sheet,
                    13,
                    62,
                    'Male'
                );

                /*
                |--------------------------------------------------------------------------
                | 16. EXTRACT FEMALE STUDENTS
                |--------------------------------------------------------------------------
                */

                $femaleStudents = $this->extractStudents(
                    $sheet,
                    64,
                    113,
                    'Female'
                );

                /*
                |--------------------------------------------------------------------------
                | 17. SAVE EXTRACTED DATA
                |--------------------------------------------------------------------------
                */

                $allRecords[] = [

                    'file_number' => $index + 1,

                    'filename' =>
                        $file->getClientOriginalName(),

                    'status' => 'SUCCESS',

                    'worksheet' => self::WORKSHEET,

                    // Use the selected semester consistently.
                    'semester' => $semesterLabel,

                    'subject' => $subject['value'],

                    'teacher' => $teacher['value'],

                    'male_students' => $maleStudents,

                    'female_students' => $femaleStudents,

                ];

            } catch (Throwable $exception) {

                $failures[] =
                    $file->getClientOriginalName()
                    . ': '
                    . $exception->getMessage();

            } finally {

                /*
                |--------------------------------------------------------------------------
                | 18. RELEASE EXCEL MEMORY
                |--------------------------------------------------------------------------
                */

                if ($spreadsheet !== null) {

                    $spreadsheet->disconnectWorksheets();
                }

                unset(
                    $spreadsheet,
                    $reader,
                    $sheet,
                    $maleStudents,
                    $femaleStudents
                );

                gc_collect_cycles();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 19. STOP IF ANY UPLOAD FAILED
        |--------------------------------------------------------------------------
        */

        if (!empty($failures)) {

            throw ValidationException::withMessages([

                'class_records' => implode(' | ', $failures),

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 20. GENERATE MASTER SHEET
        |--------------------------------------------------------------------------
        */

        try {

            $result = $generator->generate(
                $section,
                $allRecords,
                $semesterNumber
            );

        } catch (ValidationException $exception) {

            throw $exception;

        } catch (Throwable $exception) {

            report($exception);

            return back()->withErrors([

                'class_records' =>
                    'Failed to generate Master Sheet. '
                    . 'Please check the Laravel error log.',

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 21. DOWNLOAD GENERATED MASTER SHEET
        |--------------------------------------------------------------------------
        */

        return response()->download(

            $result['path'],

            $result['filename'],

            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DETECT SEMESTER FROM CLASS RECORD
    |--------------------------------------------------------------------------
    |
    | Supports:
    | FIRST SEMESTER
    | SECOND SEMESTER
    | 1ST SEMESTER
    | 2ND SEMESTER
    | SEMESTER 1
    | SEMESTER 2
    |
    */

    private function detectSemester(string $value): ?int
    {
        $value = strtoupper(trim($value));

        if ($value === '') {
            return null;
        }

        if (
            $value === '1' ||
            preg_match('/\b(FIRST|1ST)\b/', $value) ||
            preg_match('/\bSEM(?:ESTER)?\s*[-:]?\s*1\b/', $value)
        ) {
            return 1;
        }

        if (
            $value === '2' ||
            preg_match('/\b(SECOND|2ND)\b/', $value) ||
            preg_match('/\bSEM(?:ESTER)?\s*[-:]?\s*2\b/', $value)
        ) {
            return 2;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | EXTRACT VALID STUDENTS
    |--------------------------------------------------------------------------
    */

    private function extractStudents(
        Worksheet $sheet,
        int $startRow,
        int $endRow,
        string $gender
    ): array {

        $students = [];

        for ($row = $startRow; $row <= $endRow; $row++) {

            $name = $this->readRange(
                $sheet,
                'B',
                'E',
                $row
            );

            $studentName = trim(
                (string) ($name['value'] ?? '')
            );

            // Skip empty student rows.
            if (
                $studentName === '' ||
                $studentName === '0'
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | FIRST QUARTER
            |--------------------------------------------------------------------------
            */

            $firstQuarter = $this->readRange(
                $sheet,
                'F',
                'M',
                $row
            );

            /*
            |--------------------------------------------------------------------------
            | SECOND QUARTER
            |--------------------------------------------------------------------------
            */

            $secondQuarter = $this->readRange(
                $sheet,
                'N',
                'U',
                $row
            );

            $students[] = [

                'excel_row' => $row,

                'gender' => $gender,

                'student_name' => $studentName,

                'first_quarter' =>
                    $firstQuarter['value'],

                'second_quarter' =>
                    $secondQuarter['value'],

            ];
        }

        return $students;
    }

    /*
    |--------------------------------------------------------------------------
    | READ HORIZONTAL RANGE
    |--------------------------------------------------------------------------
    */

    private function readRange(
        Worksheet $sheet,
        string $startColumn,
        string $endColumn,
        int $row
    ): array {

        $start = Coordinate::columnIndexFromString(
            $startColumn
        );

        $end = Coordinate::columnIndexFromString(
            $endColumn
        );

        for ($column = $start; $column <= $end; $column++) {

            $address =
                Coordinate::stringFromColumnIndex($column)
                . $row;

            if (!$sheet->cellExists($address)) {
                continue;
            }

            $cell = $sheet->getCell($address);

            $raw = $cell->getValue();

            if (
                $raw === null ||
                (is_string($raw) && trim($raw) === '')
            ) {
                continue;
            }

            // Use cached Excel formula values.
            $value =
                $cell->getDataType() === DataType::TYPE_FORMULA
                    ? $cell->getOldCalculatedValue()
                    : $raw;

            if (is_string($value)) {
                $value = trim($value);
            }

            if (
                $value !== null &&
                $value !== ''
            ) {

                return [
                    'value' => $value,
                    'source_cell' => $address,
                ];
            }
        }

        return [
            'value' => null,
            'source_cell' => null,
        ];
    }
}
