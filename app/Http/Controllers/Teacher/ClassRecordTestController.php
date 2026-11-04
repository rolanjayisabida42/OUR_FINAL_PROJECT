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
    | EXTRACT AND GENERATE MASTER SHEET
    |--------------------------------------------------------------------------
    */

    public function extract(
        Request $request,
        Section $section,
        MasterSheetGenerator $generator
    ) {
        /*
        |--------------------------------------------------------------
        | AUTHORIZATION
        |--------------------------------------------------------------
        | Uses the classroom ownership check from your
        | existing extraction controller.
        */

        abort_unless(
            (string) ($section->adviser?->teacher?->getKey() ?? '')
                === (string) auth()->id(),
            403,
            'You are not assigned to this classroom.'
        );

        /*
        |--------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------
        */

        $validated = $request->validate([

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

            'class_records.required' =>
                'Please select at least one Class Record.',

            'class_records.max' =>
                'You can upload a maximum of 11 Class Records.',

            'class_records.*.extensions' =>
                'Only XLSX Excel files are allowed.',

            'class_records.*.max' =>
                'Each Excel file must not exceed 15 MB.',

        ]);

        /*
        |--------------------------------------------------------------
        | EXTRACT CLASS RECORDS
        |--------------------------------------------------------------
        */

        $allRecords = [];

        $failures = [];

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
                |------------------------------------------------------
                | XLSX READER
                |------------------------------------------------------
                */

                $reader = IOFactory::createReader('Xlsx');

                if (!$reader->canRead($filePath)) {

                    throw new RuntimeException(
                        'Invalid XLSX workbook.'
                    );

                }

                $worksheetNames = $reader->listWorksheetNames(
                    $filePath
                );

                if (!in_array(
                    self::WORKSHEET,
                    $worksheetNames,
                    true
                )) {

                    throw new RuntimeException(
                        'Worksheet "' . self::WORKSHEET .
                        '" was not found.'
                    );

                }

                /*
                |------------------------------------------------------
                | MEMORY OPTIMIZATION
                |------------------------------------------------------
                */

                $reader->setReadDataOnly(true);

                $reader->setLoadSheetsOnly([
                    self::WORKSHEET
                ]);

                $reader->setReadEmptyCells(false);

                $reader->setIncludeCharts(false);

                /*
                |------------------------------------------------------
                | LOAD ONLY REQUIRED EXCEL CELLS
                |------------------------------------------------------
                */

                $reader->setReadFilter(
                    new class implements IReadFilter {

                        public function readCell(
                            string $columnAddress,
                            int $row,
                            string $worksheetName = ''
                        ): bool {

                            $column =
                                Coordinate::columnIndexFromString(
                                    $columnAddress
                                );

                            // Semester and subject
                            if ($row === 8 || $row === 9) {
                                return $column >= 22 &&
                                       $column <= 28;
                            }

                            // Teacher
                            if ($row === 10) {
                                return $column >= 9 &&
                                       $column <= 18;
                            }

                            // Male student rows
                            if ($row >= 13 && $row <= 62) {
                                return $column >= 2 &&
                                       $column <= 21;
                            }

                            // Female student rows
                            if ($row >= 64 && $row <= 113) {
                                return $column >= 2 &&
                                       $column <= 21;
                            }

                            return false;
                        }
                    }
                );

                /*
                |------------------------------------------------------
                | LOAD WORKSHEET
                |------------------------------------------------------
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
                |------------------------------------------------------
                | EXTRACT SUBJECT INFORMATION
                |------------------------------------------------------
                */

                $semester = $this->readRange(
                    $sheet, 'V', 'AB', 8
                );

                $subject = $this->readRange(
                    $sheet, 'V', 'AB', 9
                );

                $teacher = $this->readRange(
                    $sheet, 'I', 'R', 10
                );

                /*
                |------------------------------------------------------
                | EXTRACT STUDENTS
                |------------------------------------------------------
                */

                $maleStudents = $this->extractStudents(
                    $sheet,
                    13,
                    62,
                    'Male'
                );

                $femaleStudents = $this->extractStudents(
                    $sheet,
                    64,
                    113,
                    'Female'
                );

                /*
                |------------------------------------------------------
                | SAVE EXTRACTED DATA IN MEMORY
                |------------------------------------------------------
                */

                $allRecords[] = [

                    'file_number' => $index + 1,

                    'filename' =>
                        $file->getClientOriginalName(),

                    'status' => 'SUCCESS',

                    'semester' => $semester['value'],

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
                |------------------------------------------------------
                | FREE EXCEL MEMORY
                |------------------------------------------------------
                */

                if ($spreadsheet !== null) {
                    $spreadsheet->disconnectWorksheets();
                }

                unset(
                    $spreadsheet,
                    $reader,
                    $sheet
                );

                gc_collect_cycles();

            }
        }

        /*
        |--------------------------------------------------------------
        | STOP IF ANY FILE FAILED
        |--------------------------------------------------------------
        */

        if (!empty($failures)) {

            throw ValidationException::withMessages([
                'class_records' => implode(' | ', $failures),
            ]);

        }

        /*
        |--------------------------------------------------------------
        | GENERATE MASTER SHEET
        |--------------------------------------------------------------
        */

        try {

            $result = $generator->generate(
                $section,
                $allRecords
            );

        } catch (ValidationException $exception) {

            throw $exception;

        } catch (Throwable $exception) {

            report($exception);

            return back()->withErrors([

                'class_records' =>
                    'Failed to generate Master Sheet. ' .
                    'Please check the Laravel error log.',

            ]);

        }

        /*
        |--------------------------------------------------------------
        | DOWNLOAD GENERATED EXCEL FILE
        |--------------------------------------------------------------
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
                $sheet, 'B', 'E', $row
            );

            $studentName = trim(
                (string) ($name['value'] ?? '')
            );

            /*
            | Skip empty student rows.
            */

            if (
                $studentName === '' ||
                $studentName === '0'
            ) {
                continue;
            }

            /*
            | First Quarter
            */

            $firstQuarter = $this->readRange(
                $sheet, 'F', 'M', $row
            );

            /*
            | Second Quarter
            */

            $secondQuarter = $this->readRange(
                $sheet, 'N', 'U', $row
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
                (
                    is_string($raw) &&
                    trim($raw) === ''
                )
            ) {
                continue;
            }

            /*
            | Use cached Excel formula values.
            */

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
