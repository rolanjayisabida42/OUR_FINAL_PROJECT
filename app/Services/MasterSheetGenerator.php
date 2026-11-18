<?php

namespace App\Services;

use App\Models\Section;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;
use Throwable;

class MasterSheetGenerator
{
    private const MAX_SUBJECTS = 11;
    private const TEMPLATE_SUBJECT_SLOTS = 10;
    private const ORIGINAL_BOYS = 40;
    private const ORIGINAL_GIRLS = 39;
    private const FIRST_SUBJECT_COLUMN = 7;
    private const SUBJECT_BLOCK_WIDTH = 4;
    private const GENERAL_AVERAGE_COLUMN = 'AU';

    /**
     * Generate a Master Sheet for the teacher-selected semester.
     * Semester: 1 = First Semester, 2 = Second Semester.
     */
    public function generate(
        Section $section,
        array $records,
        int $semesterNumber
    ): array {
        // Validate the selected semester.
        if (!in_array($semesterNumber, [1, 2], true)) {
            throw ValidationException::withMessages([
                'semester' => 'Please select First or Second Semester.',
            ]);
        }

        // Validate the number of uploaded class records.
        if (count($records) < 1 || count($records) > self::MAX_SUBJECTS) {
            throw ValidationException::withMessages([
                'class_records' => 'Upload between 1 and 11 Class Records.',
            ]);
        }

        // Get the school year assigned to this classroom.
        $section->loadMissing('schoolYear');

        $schoolYear = trim(
            (string) ($section->schoolYear?->name ?? '')
        );

        if ($schoolYear === '') {
            throw ValidationException::withMessages([
                'class_records' => 'The classroom has no assigned school year.',
            ]);
        }

        // Determine the teacher-selected semester.
        $semesterLabel = $semesterNumber === 1
            ? 'FIRST SEMESTER'
            : 'SECOND SEMESTER';

        // Validate the student roster.
        $students = $this->validateStudentNames($records);

        // Sort student names alphabetically.
        foreach (['Male', 'Female'] as $gender) {
            asort(
                $students[$gender],
                SORT_NATURAL | SORT_FLAG_CASE
            );
        }

        $boyCount = count($students['Male']);
        $girlCount = count($students['Female']);

        /*
        |--------------------------------------------------------------------------
        | PREPARE SUBJECTS AND GRADES
        |--------------------------------------------------------------------------
        */

        $subjects = [];
        $grades = [];
        $usedSubjects = [];
        $expectedSemester = null;

        foreach ($records as $index => $record) {
            $filename = $record['filename']
                ?? 'File ' . ($index + 1);

            $subject = trim(
                (string) ($record['subject'] ?? '')
            );

            $semester = trim(
                (string) ($record['semester'] ?? '')
            );

            if ($subject === '' || $subject === '0') {
                throw ValidationException::withMessages([
                    'class_records' => "Missing subject name in {$filename}.",
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATE DUPLICATE SUBJECTS
            |--------------------------------------------------------------------------
            |
            | ENGLISH 2  != ENGLISH 02
            | COMPUTER 2 != COMPUTER 02
            |
            */

            $subjectKey = mb_strtoupper(
                trim(
                    preg_replace('/\s+/u', ' ', $subject)
                    ?? $subject
                ),
                'UTF-8'
            );

            if (isset($usedSubjects[$subjectKey])) {
                $previous = $usedSubjects[$subjectKey];

                throw ValidationException::withMessages([
                    'class_records' =>
                        "Duplicate subject detected: '{$subject}' "
                        . "from '{$filename}' matches "
                        . "'{$previous['subject']}' "
                        . "from '{$previous['filename']}'.",
                ]);
            }

            $usedSubjects[$subjectKey] = [
                'subject' => $subject,
                'filename' => $filename,
            ];

            /*
            |--------------------------------------------------------------------------
            | CHECK SEMESTER CONSISTENCY
            |--------------------------------------------------------------------------
            */

            $semesterKey = $this->normalize($semester);

            if ($semesterKey === '') {
                throw ValidationException::withMessages([
                    'class_records' => "Missing semester in {$filename}.",
                ]);
            }

            if (
                $expectedSemester !== null &&
                $expectedSemester !== $semesterKey
            ) {
                throw ValidationException::withMessages([
                    'class_records' =>
                        'The uploaded Class Records belong to different semesters.',
                ]);
            }

            $expectedSemester = $semesterKey;

            $subjects[$index] = $subject;

            $grades[$index] = [
                'Male' => [],
                'Female' => [],
            ];

            /*
            |--------------------------------------------------------------------------
            | EXTRACT STUDENT GRADES
            |--------------------------------------------------------------------------
            */

            foreach ([
                'male_students' => 'Male',
                'female_students' => 'Female',
            ] as $field => $gender) {

                foreach (($record[$field] ?? []) as $student) {
                    $name = trim(
                        (string) ($student['student_name'] ?? '')
                    );

                    if ($name === '' || $name === '0') {
                        continue;
                    }

                    $key = $this->normalize($name);

                    $grades[$index][$gender][$key] = [
                        'min' => $student['first_quarter'] ?? null,
                        'fin' => $student['second_quarter'] ?? null,
                    ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FIND MASTER SHEET TEMPLATE
        |--------------------------------------------------------------------------
        */

        $template = storage_path(
            'app/private/templates/master-class/MasterSheet Template.xlsx'
        );

        if (!is_file($template) || !is_readable($template)) {
            throw ValidationException::withMessages([
                'class_records' =>
                    'Master Sheet template not found: ' . $template,
            ]);
        }

        $workbook = null;
        $outputPath = null;

        try {

            /*
            |--------------------------------------------------------------------------
            | LOAD EXCEL TEMPLATE
            |--------------------------------------------------------------------------
            */

            $reader = IOFactory::createReader('Xlsx');

            $reader->setReadDataOnly(false);

            $workbook = $reader->load($template);

            $sheet = $workbook->getActiveSheet();

            /*
            |--------------------------------------------------------------------------
            | NEW FEATURE: AUTOMATIC WORKSHEET NAME
            |--------------------------------------------------------------------------
            |
            | FIRST SEMESTER  = 1ST-SEM-MIDTERM
            | SECOND SEMESTER = 2ND-SEM-MIDTERM
            |
            | This renames the worksheet tab inside Excel.
            |
            */

            $sheetName = $semesterNumber === 1
                ? '1ST-SEM'
                : '2ND-SEM';

            $sheet->setTitle($sheetName);

            /*
            |--------------------------------------------------------------------------
            | EXPAND BOYS' ROWS
            |--------------------------------------------------------------------------
            */

            $extraBoys = max(
                0,
                $boyCount - self::ORIGINAL_BOYS
            );

            if ($extraBoys > 0) {
                $sheet->insertNewRowBefore(51, $extraBoys);

                for (
                    $row = 51;
                    $row <= 50 + $extraBoys;
                    $row++
                ) {
                    $this->cloneStudentRow(
                        $sheet,
                        50,
                        $row
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | EXPAND GIRLS' ROWS
            |--------------------------------------------------------------------------
            */

            $girlStart = 52 + $extraBoys;

            $extraGirls = max(
                0,
                $girlCount - self::ORIGINAL_GIRLS
            );

            if ($extraGirls > 0) {
                $insertAt = 91 + $extraBoys;

                $sheet->insertNewRowBefore(
                    $insertAt,
                    $extraGirls
                );

                for (
                    $row = $insertAt;
                    $row < $insertAt + $extraGirls;
                    $row++
                ) {
                    $this->cloneStudentRow(
                        $sheet,
                        90 + $extraBoys,
                        $row
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | STUDENT ROW POSITIONS
            |--------------------------------------------------------------------------
            */

            $boyRows = range(
                11,
                50 + $extraBoys
            );

            $girlRows = range(
                $girlStart,
                90 + $extraBoys + $extraGirls
            );

            $lastStudentRow =
                90 + $extraBoys + $extraGirls;

            /*
            |--------------------------------------------------------------------------
            | DYNAMIC SUBJECT BLOCKS
            |--------------------------------------------------------------------------
            |
            | 10 subjects:
            | General Average = AU
            |
            | 11 subjects:
            | New subject = AU:AW
            | General Average = AY
            |
            */

            $this->expandSubjectBlocks(
                $sheet,
                count($subjects),
                $lastStudentRow
            );

            $extraSubjectBlocks = max(
                0,
                count($subjects) - self::TEMPLATE_SUBJECT_SLOTS
            );

            $generalAverageColumn =
                Coordinate::stringFromColumnIndex(
                    Coordinate::columnIndexFromString(
                        self::GENERAL_AVERAGE_COLUMN
                    )
                    + (
                        $extraSubjectBlocks
                        * self::SUBJECT_BLOCK_WIDTH
                    )
                );

            /*
            |--------------------------------------------------------------------------
            | CLEAR OLD STUDENT INPUT VALUES
            |--------------------------------------------------------------------------
            */

            $slots = max(
                self::TEMPLATE_SUBJECT_SLOTS,
                count($subjects)
            );

            foreach ([$boyRows, $girlRows] as $rows) {

                foreach ($rows as $row) {

                    $sheet->setCellValue(
                        'B' . $row,
                        null
                    );

                    for (
                        $subjectIndex = 0;
                        $subjectIndex < $slots;
                        $subjectIndex++
                    ) {

                        $base = $this->subjectStart(
                            $subjectIndex
                        );

                        $sheet->setCellValue(
                            $this->column($base) . $row,
                            null
                        );

                        $sheet->setCellValue(
                            $this->column($base + 1) . $row,
                            null
                        );
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CLEAR OLD SUBJECT HEADINGS
            |--------------------------------------------------------------------------
            */

            for ($i = 0; $i < $slots; $i++) {
                $sheet->setCellValue(
                    $this->column(
                        $this->subjectStart($i)
                    ) . '8',
                    null
                );
            }

            /*
            |--------------------------------------------------------------------------
            | WRITE SUBJECT HEADINGS
            |--------------------------------------------------------------------------
            */

            foreach ($subjects as $index => $subject) {

                $sheet->setCellValueExplicit(
                    $this->column(
                        $this->subjectStart($index)
                    ) . '8',
                    $subject,
                    DataType::TYPE_STRING
                );
            }

            /*
            |--------------------------------------------------------------------------
            | WRITE STUDENTS AND GRADES
            |--------------------------------------------------------------------------
            */

            foreach ([
                'Male' => $boyRows,
                'Female' => $girlRows,
            ] as $gender => $rows) {

                $position = 0;

                foreach ($students[$gender] as $key => $name) {

                    $row = $rows[$position++];

                    $sheet->setCellValueExplicit(
                        'B' . $row,
                        $name,
                        DataType::TYPE_STRING
                    );

                    $subjectAverageCells = [];

                    /*
                    |--------------------------------------------------------------------------
                    | GRADES FOR EACH SUBJECT
                    |--------------------------------------------------------------------------
                    */

                    foreach ($subjects as $index => $subject) {

                        $base = $this->subjectStart($index);

                        $minColumn = $this->column($base);
                        $finColumn = $this->column($base + 1);
                        $aveColumn = $this->column($base + 2);

                        $pair = $grades[$index][$gender][$key];

                        // First quarter grade.
                        $this->writeGrade(
                            $sheet,
                            $minColumn . $row,
                            $pair['min']
                        );

                        // Second quarter grade.
                        $this->writeGrade(
                            $sheet,
                            $finColumn . $row,
                            $pair['fin']
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | SUBJECT AVERAGE
                        |--------------------------------------------------------------------------
                        */

                        $averageAddress = $aveColumn . $row;

                        if (
                            !$sheet->getCell(
                                $averageAddress
                            )->isFormula()
                        ) {
                            $formula = '=IF(COUNT('
                                . $minColumn . $row
                                . ':'
                                . $finColumn . $row
                                . ')<2,"",ROUND(AVERAGE('
                                . $minColumn . $row
                                . ':'
                                . $finColumn . $row
                                . '),0))';

                            $sheet->setCellValue(
                                $averageAddress,
                                $formula
                            );
                        }

                        $subjectAverageCells[] =
                            $averageAddress;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | GENERAL AVERAGE
                    |--------------------------------------------------------------------------
                    */

                    $references = implode(
                        ',',
                        $subjectAverageCells
                    );

                    $generalAverageFormula = '=IF(COUNT('
                        . $references
                        . ')=0,"",ROUND(AVERAGE('
                        . $references
                        . '),0))';

                    $sheet->setCellValue(
                        $generalAverageColumn . $row,
                        $generalAverageFormula
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE OUTPUT FOLDER
            |--------------------------------------------------------------------------
            */

            $folder = storage_path(
                'app/private/generated/master-sheets'
            );

            if (
                !is_dir($folder) &&
                !mkdir($folder, 0775, true) &&
                !is_dir($folder)
            ) {
                throw new RuntimeException(
                    'Unable to create Master Sheet directory.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE FILENAME
            |--------------------------------------------------------------------------
            */

            $filename = 'MasterSheet_'
                . Str::slug(
                    (string) $section->name,
                    '_'
                )
                . '_Sem' . $semesterNumber
                . '_' . now()->format('Ymd_His')
                . '_' . Str::random(6)
                . '.xlsx';

            $outputPath = $folder
                . DIRECTORY_SEPARATOR
                . $filename;

            /*
            |--------------------------------------------------------------------------
            | UPDATE MASTER SHEET HEADER
            |--------------------------------------------------------------------------
            |
            | The header is in merged cells B5:AY5.
            | Write the semester and school year to B5.
            |
            | Example:
            | SECOND SEMESTER (SY: 2025-2026)
            |
            */

            $header = $semesterLabel
                . ' (SY: ' . $schoolYear . ')';

            $sheet->setCellValueExplicit(
                'B5',
                $header,
                DataType::TYPE_STRING
            );

            /*
            |--------------------------------------------------------------------------
            | SAVE GENERATED MASTER SHEET
            |--------------------------------------------------------------------------
            */

            $writer = IOFactory::createWriter(
                $workbook,
                'Xlsx'
            );

            $writer->setPreCalculateFormulas(false);

            $writer->save($outputPath);

            /*
            |--------------------------------------------------------------------------
            | RETURN GENERATED FILE
            |--------------------------------------------------------------------------
            */

            return [
                'path' => $outputPath,
                'filename' => $filename,
                'boys' => $boyCount,
                'girls' => $girlCount,
                'subjects' => count($subjects),
                'general_average_column' => $generalAverageColumn,
            ];

        } catch (Throwable $exception) {

            if (
                $outputPath !== null &&
                is_file($outputPath)
            ) {
                @unlink($outputPath);
            }

            throw $exception;

        } finally {

            if ($workbook !== null) {
                $workbook->disconnectWorksheets();
            }

            gc_collect_cycles();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXPAND SUBJECT BLOCKS
    |--------------------------------------------------------------------------
    */

    private function expandSubjectBlocks(
        Worksheet $sheet,
        int $subjectCount,
        int $lastStudentRow
    ): void {

        for (
            $index = self::TEMPLATE_SUBJECT_SLOTS;
            $index < $subjectCount;
            $index++
        ) {

            $targetStart = $this->subjectStart($index);
            $sourceStart = $this->subjectStart($index - 1);
            $targetColumn = $this->column($targetStart);

            $sheet->insertNewColumnBefore(
                $targetColumn,
                self::SUBJECT_BLOCK_WIDTH
            );

            /*
            |--------------------------------------------------------------------------
            | COPY SUBJECT FORMATTING
            |--------------------------------------------------------------------------
            */

            for (
                $offset = 0;
                $offset < self::SUBJECT_BLOCK_WIDTH;
                $offset++
            ) {

                $sourceColumn = $this->column(
                    $sourceStart + $offset
                );

                $newColumn = $this->column(
                    $targetStart + $offset
                );

                $sheet->getColumnDimension($newColumn)->setWidth(
                    $sheet->getColumnDimension(
                        $sourceColumn
                    )->getWidth()
                );

                for (
                    $row = 1;
                    $row <= $lastStudentRow + 8;
                    $row++
                ) {

                    $from = $sourceColumn . $row;
                    $to = $newColumn . $row;

                    $sheet->duplicateStyle(
                        $sheet->getStyle($from),
                        $to
                    );

                    // Copy MIN, FIN and AVE labels.
                    if (
                        ($row === 9 || $row === 10) &&
                        $sheet->cellExists($from)
                    ) {

                        if (
                            $sheet->getCell($from)->isFormula()
                        ) {

                            $sheet->copyFormula(
                                $from,
                                $to
                            );

                        } else {

                            $value = $sheet->getCell(
                                $from
                            )->getValue();

                            if ($value !== null) {
                                $sheet->setCellValue(
                                    $to,
                                    $value
                                );
                            }
                        }
                    }

                    // Copy subject-average formulas.
                    if (
                        $offset === 2 &&
                        $row >= 11 &&
                        $sheet->cellExists($from) &&
                        $sheet->getCell($from)->isFormula()
                    ) {

                        $sheet->copyFormula(
                            $from,
                            $to
                        );
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | MERGE NEW SUBJECT HEADING
            |--------------------------------------------------------------------------
            */

            $heading = $this->column($targetStart)
                . '8:'
                . $this->column($targetStart + 2)
                . '8';

            if (
                !in_array(
                    $heading,
                    $sheet->getMergeCells(),
                    true
                )
            ) {
                $sheet->mergeCells($heading);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CLONE STUDENT ROW
    |--------------------------------------------------------------------------
    */

    private function cloneStudentRow(
        Worksheet $sheet,
        int $sourceRow,
        int $targetRow
    ): void {

        $sheet->getRowDimension($targetRow)->setRowHeight(
            $sheet->getRowDimension(
                $sourceRow
            )->getRowHeight()
        );

        for (
            $column = 1;
            $column <= Coordinate::columnIndexFromString('AT');
            $column++
        ) {

            $letter = $this->column($column);

            $sheet->duplicateStyle(
                $sheet->getStyle(
                    $letter . $sourceRow
                ),
                $letter . $targetRow
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MERGE STUDENT NAME CELLS
        |--------------------------------------------------------------------------
        */

        $merged = 'B' . $targetRow
            . ':E' . $targetRow;

        if (
            !in_array(
                $merged,
                $sheet->getMergeCells(),
                true
            )
        ) {
            $sheet->mergeCells($merged);
        }

        /*
        |--------------------------------------------------------------------------
        | COPY SUBJECT AVERAGE FORMULAS
        |--------------------------------------------------------------------------
        */

        for (
            $index = 0;
            $index < self::TEMPLATE_SUBJECT_SLOTS;
            $index++
        ) {

            $aveColumn = $this->column(
                $this->subjectStart($index) + 2
            );

            $from = $aveColumn . $sourceRow;

            if (
                $sheet->cellExists($from) &&
                $sheet->getCell($from)->isFormula()
            ) {

                $sheet->copyFormula(
                    $from,
                    $aveColumn . $targetRow
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATE STUDENT NAMES
    |--------------------------------------------------------------------------
    */

    private function validateStudentNames(
        array $records
    ): array {

        $reference = $this->studentRoster(
            $records[0]
        );

        $referenceFile = $records[0]['filename']
            ?? 'First file';

        if (
            count($reference['Male']) +
            count($reference['Female']) === 0
        ) {
            throw ValidationException::withMessages([
                'class_records' =>
                    'No students found in the first Class Record.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | COMPARE STUDENT ROSTERS
        |--------------------------------------------------------------------------
        */

        foreach ($records as $index => $record) {

            if ($index === 0) {
                continue;
            }

            $current = $this->studentRoster($record);

            foreach (['Male', 'Female'] as $gender) {

                $missing = array_diff_key(
                    $reference[$gender],
                    $current[$gender]
                );

                $extra = array_diff_key(
                    $current[$gender],
                    $reference[$gender]
                );

                if ($missing || $extra) {

                    throw ValidationException::withMessages([
                        'class_records' =>
                            'Student roster mismatch in "'
                            . (
                                $record['filename']
                                ?? 'File ' . ($index + 1)
                            )
                            . '" (' . $gender
                            . '), compared with "' . $referenceFile
                            . '". Missing: '
                            . (implode(', ', $missing) ?: 'none')
                            . '. Extra: '
                            . (implode(', ', $extra) ?: 'none')
                            . '. No Master Sheet was saved.',
                    ]);
                }
            }
        }

        return $reference;
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD STUDENT ROSTER
    |--------------------------------------------------------------------------
    */

    private function studentRoster(
        array $record
    ): array {

        $roster = [
            'Male' => [],
            'Female' => [],
        ];

        foreach ([
            'male_students' => 'Male',
            'female_students' => 'Female',
        ] as $field => $gender) {

            foreach (($record[$field] ?? []) as $student) {

                $name = trim(
                    (string) ($student['student_name'] ?? '')
                );

                if ($name === '' || $name === '0') {
                    continue;
                }

                $key = $this->normalize($name);

                if ($key === '') {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | CHECK DUPLICATE STUDENT
                |--------------------------------------------------------------------------
                */

                if (isset($roster[$gender][$key])) {

                    throw ValidationException::withMessages([
                        'class_records' =>
                            'Duplicate student "' . $name
                            . '" in '
                            . (
                                $record['filename']
                                ?? 'Class Record'
                            )
                            . '.',
                    ]);
                }

                $other = $gender === 'Male'
                    ? 'Female'
                    : 'Male';

                if (isset($roster[$other][$key])) {

                    throw ValidationException::withMessages([
                        'class_records' =>
                            'Student "' . $name
                            . '" appears in both gender lists.',
                    ]);
                }

                $roster[$gender][$key] = $name;
            }
        }

        return $roster;
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE STUDENT NAMES
    |--------------------------------------------------------------------------
    */

    private function normalize(string $name): string
    {
        $name = mb_strtoupper(
            trim($name),
            'UTF-8'
        );

        $name = preg_replace(
            '/[^\p{L}\p{N}]+/u',
            ' ',
            $name
        ) ?? $name;

        return trim(
            preg_replace('/\s+/u', ' ', $name)
            ?? $name
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SUBJECT STARTING COLUMN
    |--------------------------------------------------------------------------
    */

    private function subjectStart(int $index): int
    {
        return self::FIRST_SUBJECT_COLUMN
            + $index * self::SUBJECT_BLOCK_WIDTH;
    }

    /*
    |--------------------------------------------------------------------------
    | COLUMN NUMBER TO LETTER
    |--------------------------------------------------------------------------
    */

    private function column(int $index): string
    {
        return Coordinate::stringFromColumnIndex($index);
    }

    /*
    |--------------------------------------------------------------------------
    | WRITE GRADE
    |--------------------------------------------------------------------------
    */

    private function writeGrade(
        Worksheet $sheet,
        string $cell,
        mixed $value
    ): void {

        if (
            $value === null ||
            (
                is_string($value) &&
                trim($value) === ''
            )
        ) {

            $sheet->setCellValue($cell, null);

        } elseif (is_numeric($value)) {

            $sheet->setCellValue(
                $cell,
                (float) $value
            );

        } else {

            $sheet->setCellValueExplicit(
                $cell,
                (string) $value,
                DataType::TYPE_STRING
            );
        }
    }
}
