<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;
use ZipArchive;

class StudentCardGenerator
{
    /*
    |--------------------------------------------------------------------------
    | EXACT WORD TEMPLATE ROW POSITIONS
    |--------------------------------------------------------------------------
    |
    | Zero-based rows in the fifth table of:
    | 11-ICT-STUDENT-CARD.docx
    |
    */

    private const WORD_ROWS = [
        1 => [
            'ENGLISH1'   => 3,
            'FILIPINO1'  => 4,
            'MATH1'      => 5,
            'PE1'        => 6,
            'ETECH1'     => 7,
            'SCIENCE1'   => 8,
            'PERDEV'     => 9,
            'EFAPP'      => 10,
            'COMPUTER1'  => 12,
            'COMPUTER01' => 13,
        ],

        2 => [
            'ENGLISH2'   => 18,
            'MATH2'      => 19,
            'FILIPINO2'  => 20,
            'UCSP'       => 21,
            'SCIENCE2'   => 22,
            'ENGLISH02'  => 23,
            'PE2'        => 24,
            'ETECH2'     => 25,
            'RESEARCH1'  => 26,
            'COMPUTER02' => 28,
            'COMPUTER2'  => 29,
        ],
    ];

    private const AVERAGE_ROWS = [
        1 => 14,
        2 => 30,
    ];

    private const WORD_NAMESPACE =
        'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    /*
    |--------------------------------------------------------------------------
    | READ STUDENTS FROM MASTER SHEET
    |--------------------------------------------------------------------------
    |
    | Semester 1 = First Semester
    | Semester 2 = Second Semester
    |
    */

    public function readStudents(
        string $xlsxPath,
        int $semesterNumber
    ): array {

        if (!isset(self::WORD_ROWS[$semesterNumber])) {
            throw ValidationException::withMessages([
                'master_sheet' => 'Invalid semester selected.',
            ]);
        }

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);

        $workbook = $reader->load($xlsxPath);

        try {

            $sheet = $workbook->getActiveSheet();

            $this->validateSemester(
                $sheet,
                $semesterNumber
            );

            /*
            |--------------------------------------------------------------------------
            | IDENTIFY SUBJECTS
            |--------------------------------------------------------------------------
            */

            $expectedCodes = array_keys(
                self::WORD_ROWS[$semesterNumber]
            );

            $subjects = [];
            $unknownSubjects = [];

            /*
            |--------------------------------------------------------------------------
            | READ SUBJECT HEADINGS
            |--------------------------------------------------------------------------
            |
            | Subject columns:
            | G8, K8, O8, S8, etc.
            |
            | Up to 11 subjects.
            |
            */

            for ($i = 0; $i < 11; $i++) {

                $column = Coordinate::stringFromColumnIndex(
                    7 + $i * 4
                );

                $heading = trim(
                    (string) $sheet->getCell(
                        $column . '8'
                    )->getValue()
                );

                /*
                |--------------------------------------------------------------------------
                | IMPORTANT FIX: IGNORE AVERAGE HEADINGS
                |--------------------------------------------------------------------------
                |
                | AVE is not a subject.
                |
                | Ignore:
                | AVE
                | AVG
                | AVERAGE
                | GENERAL AVERAGE
                | GENERAL AVE
                |
                */

                if ($this->isAverageHeading($heading)) {
                    continue;
                }

                $code = $this->subjectCode(
                    $heading
                );

                /*
                |--------------------------------------------------------------------------
                | CHECK EXPECTED SUBJECTS
                |--------------------------------------------------------------------------
                */

                if (!in_array(
                    $code,
                    $expectedCodes,
                    true
                )) {

                    $unknownSubjects[] = $heading;

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | CHECK DUPLICATE SUBJECT
                |--------------------------------------------------------------------------
                */

                if (in_array(
                    $code,
                    $subjects,
                    true
                )) {

                    throw ValidationException::withMessages([
                        'master_sheet' =>
                            "Duplicate subject '{$heading}' in the Excel file.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | KEEP ACTUAL EXCEL SUBJECT POSITION
                |--------------------------------------------------------------------------
                */

                $subjects[$i] = $code;
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATE REQUIRED SUBJECTS
            |--------------------------------------------------------------------------
            */

            $missing = array_diff(
                $expectedCodes,
                array_values($subjects)
            );

            if ($missing || $unknownSubjects) {

                $label = $semesterNumber === 1
                    ? 'First'
                    : 'Second';

                throw ValidationException::withMessages([
                    'master_sheet' =>
                        $label
                        . ' Semester Master Sheet does not match '
                        . 'the Grade 11 ICT Student Card. Missing: '
                        . (implode(', ', $missing) ?: 'none')
                        . '. Unrecognized: '
                        . (implode(', ', $unknownSubjects) ?: 'none')
                        . '.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | FIND FEMALE STUDENT SECTION
            |--------------------------------------------------------------------------
            */

            $maxRow = min(
                $sheet->getHighestDataRow(),
                500
            );

            $femaleHeaderRow = 51;

            for ($row = 11; $row <= $maxRow; $row++) {

                $label = '';

                foreach (
                    ['A', 'B', 'C', 'D', 'E', 'F'] as $column
                ) {

                    $label .= ' ' . (string) $sheet->getCell(
                        $column . $row
                    )->getValue();
                }

                if (preg_match(
                    '/\bFEMALE\b|\bGIRLS\b/i',
                    $label
                )) {

                    $femaleHeaderRow = $row;

                    break;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | EXTRACT STUDENTS AND GRADES
            |--------------------------------------------------------------------------
            */

            $students = [];
            $seen = [];

            for ($row = 11; $row <= $maxRow; $row++) {

                $name = trim(
                    (string) $sheet->getCell(
                        'B' . $row
                    )->getValue()
                );

                /*
                |--------------------------------------------------------------------------
                | VALIDATE STUDENT NAME
                |--------------------------------------------------------------------------
                */

                if (
                    $name === '' ||
                    !str_contains($name, ',')
                ) {
                    continue;
                }

                $key = $this->normalizeName($name);

                if (isset($seen[$key])) {

                    throw ValidationException::withMessages([
                        'master_sheet' =>
                            "Duplicate student '{$name}' at Excel row {$row}.",
                    ]);
                }

                $seen[$key] = true;

                $grades = [];

                /*
                |--------------------------------------------------------------------------
                | READ EACH SUBJECT'S GRADES
                |--------------------------------------------------------------------------
                */

                foreach ($subjects as $index => $code) {

                    $firstIndex = 7 + $index * 4;

                    $midColumn =
                        Coordinate::stringFromColumnIndex(
                            $firstIndex
                        );

                    $finColumn =
                        Coordinate::stringFromColumnIndex(
                            $firstIndex + 1
                        );

                    $aveColumn =
                        Coordinate::stringFromColumnIndex(
                            $firstIndex + 2
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | FIRST QUARTER
                    |--------------------------------------------------------------------------
                    */

                    $mid = $this->grade(
                        $sheet->getCell(
                            $midColumn . $row
                        )->getValue()
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | SECOND QUARTER
                    |--------------------------------------------------------------------------
                    */

                    $fin = $this->grade(
                        $sheet->getCell(
                            $finColumn . $row
                        )->getValue()
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | SUBJECT FINAL GRADE
                    |--------------------------------------------------------------------------
                    */

                    $ave = $this->grade(
                        $sheet->getCell(
                            $aveColumn . $row
                        )->getValue()
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | FORMULA FALLBACK
                    |--------------------------------------------------------------------------
                    |
                    | Calculate average when Excel has a formula
                    | without a cached numeric result.
                    |
                    */

                    if (
                        $ave === null &&
                        $mid !== null &&
                        $fin !== null
                    ) {

                        $ave = round(
                            ($mid + $fin) / 2,
                            0,
                            PHP_ROUND_HALF_UP
                        );
                    }

                    $grades[$code] = [
                        'mid' => $mid,
                        'fin' => $fin,
                        'ave' => $ave,
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | CALCULATE SEMESTER GENERAL AVERAGE
                |--------------------------------------------------------------------------
                */

                $averages = array_values(
                    array_filter(
                        array_column($grades, 'ave'),
                        static fn ($value) => $value !== null
                    )
                );

                $generalAverage =
                    count($averages) === count($expectedCodes)

                    ? number_format(
                        array_sum($averages) /
                        count($expectedCodes),
                        3,
                        '.',
                        ''
                    )

                    : '';

                /*
                |--------------------------------------------------------------------------
                | STORE STUDENT INFORMATION
                |--------------------------------------------------------------------------
                */

                $students[] = [
                    'name' => $name,

                    'gender' => $row > $femaleHeaderRow
                        ? 'Female'
                        : 'Male',

                    'grades' => $grades,

                    'general_average' => $generalAverage,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | CHECK STUDENT COUNT
            |--------------------------------------------------------------------------
            */

            if (!$students) {

                throw ValidationException::withMessages([
                    'master_sheet' =>
                        'No students found in column B. '
                        . 'Use LASTNAME, FIRSTNAME format.',
                ]);
            }

            return $students;

        } finally {

            $workbook->disconnectWorksheets();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | NEW HELPER: IGNORE AVERAGE HEADINGS
    |--------------------------------------------------------------------------
    |
    | This fixes:
    |
    | Missing: none. Unrecognized: AVE.
    |
    */

    private function isAverageHeading(
        string $heading
    ): bool {

        $key = preg_replace(
            '/[^A-Z0-9]/',
            '',
            strtoupper(trim($heading))
        ) ?? '';

        return $key === '' || in_array(
            $key,
            [
                'AVE',
                'AVG',
                'AVERAGE',
                'GENERALAVE',
                'GENERALAVG',
                'GENERALAVERAGE',
                'GENAVE',
                'GENAVG',
                'GENAVERAGE',
                'SEMESTERAVERAGE',
                'FINALAVERAGE',
            ],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | COMBINE FIRST AND SECOND SEMESTER STUDENTS
    |--------------------------------------------------------------------------
    |
    | Match students using normalized names.
    |
    | One student = One Word Student Card
    |
    */

    public function combineStudents(
        array $firstSemester,
        array $secondSemester
    ): array {

        $combined = [];

        $firstNames = [];
        $secondNames = [];

        foreach (
            [
                1 => $firstSemester,
                2 => $secondSemester,
            ] as $semester => $list
        ) {

            foreach ($list as $student) {

                $key = $this->normalizeName(
                    $student['name']
                );

                if ($semester === 1) {

                    $firstNames[$key] = true;

                } else {

                    $secondNames[$key] = true;
                }

                /*
                |--------------------------------------------------------------------------
                | CREATE STUDENT RECORD
                |--------------------------------------------------------------------------
                */

                if (!isset($combined[$key])) {

                    $combined[$key] = [
                        'name' => $student['name'],

                        'gender' => $student['gender'],

                        'semesters' => [
                            1 => [
                                'grades' => [],
                                'general_average' => '',
                            ],

                            2 => [
                                'grades' => [],
                                'general_average' => '',
                            ],
                        ],
                    ];

                } elseif (
                    $combined[$key]['gender'] !==
                    $student['gender']
                ) {

                    throw ValidationException::withMessages([
                        'master_sheet' =>
                            "Gender mismatch for '{$student['name']}' between semesters.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | INSERT GRADES INTO THE CORRECT SEMESTER
                |--------------------------------------------------------------------------
                */

                $combined[$key]['semesters'][$semester] = [
                    'grades' => $student['grades'],

                    'general_average' =>
                        $student['general_average'],
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK CLASSROOM CONSISTENCY
        |--------------------------------------------------------------------------
        */

        if (
            $firstSemester &&
            $secondSemester &&
            !array_intersect_key(
                $firstNames,
                $secondNames
            )
        ) {

            throw ValidationException::withMessages([
                'master_sheet' =>
                    'The two Master Sheets have no matching students. '
                    . 'Please upload files from the same classroom.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SORT STUDENTS
        |--------------------------------------------------------------------------
        */

        $result = array_values($combined);

        usort(
            $result,
            static function (array $a, array $b): int {

                $genderCompare =
                    ($a['gender'] === 'Male' ? 0 : 1)
                    <=>
                    ($b['gender'] === 'Male' ? 0 : 1);

                return $genderCompare
                    ?: strnatcasecmp(
                        $a['name'],
                        $b['name']
                    );
            }
        );

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATE EXCEL SEMESTER
    |--------------------------------------------------------------------------
    |
    | Check both:
    |
    | Cell B5
    | Worksheet tab name
    |
    */

    private function validateSemester(
        Worksheet $sheet,
        int $expected
    ): void {

        $heading = strtoupper(
            trim(
                (string) $sheet->getCell('B5')->getValue()
            )
        );

        $tab = strtoupper(
            trim($sheet->getTitle())
        );

        $headerSemester = $this->detectSemester(
            $heading
        );

        $tabSemester = $this->detectSemester(
            $tab
        );

        /*
        |--------------------------------------------------------------------------
        | CHECK HEADER AND TAB CONSISTENCY
        |--------------------------------------------------------------------------
        */

        if (
            $headerSemester !== null &&
            $tabSemester !== null &&
            $headerSemester !== $tabSemester
        ) {

            throw ValidationException::withMessages([
                'master_sheet' =>
                    'The semester in cell B5 does not match '
                    . 'the Excel worksheet tab.',
            ]);
        }

        $actual = $headerSemester ?? $tabSemester;

        /*
        |--------------------------------------------------------------------------
        | CHECK SELECTED SEMESTER
        |--------------------------------------------------------------------------
        */

        if (
            $actual !== null &&
            $actual !== $expected
        ) {

            throw ValidationException::withMessages([
                'master_sheet' =>
                    'You uploaded a '
                    . ($actual === 1 ? 'First' : 'Second')
                    . ' Semester file into the '
                    . ($expected === 1 ? 'First' : 'Second')
                    . ' Semester slot.',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DETECT SEMESTER LABEL
    |--------------------------------------------------------------------------
    */

    private function detectSemester(
        string $text
    ): ?int {

        if (preg_match(
            '/FIRST\s+SEMESTER|1ST[-\s_]*SEM/',
            $text
        )) {
            return 1;
        }

        if (preg_match(
            '/SECOND\s+SEMESTER|2ND[-\s_]*SEM/',
            $text
        )) {
            return 2;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE STUDENT NAME
    |--------------------------------------------------------------------------
    */

    private function normalizeName(
        string $name
    ): string {

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
    | NORMALIZE SUBJECT CODE
    |--------------------------------------------------------------------------
    */

    private function subjectCode(
        string $subject
    ): string {

        $key = preg_replace(
            '/[^A-Z0-9]/',
            '',
            strtoupper($subject)
        ) ?? '';

        return $key === 'AFAPP'
            ? 'EFAPP'
            : $key;
    }

    /*
    |--------------------------------------------------------------------------
    | READ NUMERIC GRADE
    |--------------------------------------------------------------------------
    */

    private function grade(mixed $value): ?float
    {
        return is_numeric($value)
            ? (float) $value
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE STUDENT CARDS ZIP
    |--------------------------------------------------------------------------
    */

    public function makeZip(
        array $students,
        array $classroom,
        string $template
    ): string {

        /*
        |--------------------------------------------------------------------------
        | VALIDATE WORD TEMPLATE
        |--------------------------------------------------------------------------
        */

        if (
            !is_file($template) ||
            !is_readable($template)
        ) {

            throw ValidationException::withMessages([
                'master_sheet' =>
                    'Student Card Word template was not found.',
            ]);
        }

        if (!$students) {

            throw ValidationException::withMessages([
                'master_sheet' => 'No students to generate.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE OUTPUT DIRECTORY
        |--------------------------------------------------------------------------
        */

        $directory = storage_path(
            'app/private/generated/student-cards'
        );

        if (
            !is_dir($directory) &&
            !mkdir($directory, 0775, true) &&
            !is_dir($directory)
        ) {

            throw new RuntimeException(
                'Unable to create Student Card output folder.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE ZIP FILE
        |--------------------------------------------------------------------------
        */

        $zipPath = $directory
            . '/StudentCards_'
            . Str::random(12)
            . '.zip';

        $zip = new ZipArchive();

        if (
            $zip->open(
                $zipPath,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            ) !== true
        ) {

            throw new RuntimeException(
                'Could not create Student Cards ZIP.'
            );
        }

        $temporaryDocs = [];

        $completed = false;

        /*
        |--------------------------------------------------------------------------
        | GENERATE ONE WORD DOCUMENT PER STUDENT
        |--------------------------------------------------------------------------
        */

        try {

            foreach ($students as $index => $student) {

                $docxPath = $directory
                    . '/working_'
                    . Str::random(16)
                    . '.docx';

                $temporaryDocs[] = $docxPath;

                /*
                |--------------------------------------------------------------------------
                | FILL WORD TEMPLATE
                |--------------------------------------------------------------------------
                */

                $this->fillWordTemplate(
                    $template,
                    $docxPath,
                    $student,
                    $classroom
                );

                /*
                |--------------------------------------------------------------------------
                | GENERATE WORD FILENAME
                |--------------------------------------------------------------------------
                */

                $filename = sprintf(
                    '%03d_%s.docx',
                    $index + 1,
                    Str::slug(
                        $student['name'],
                        '_'
                    )
                );

                /*
                |--------------------------------------------------------------------------
                | ADD WORD DOCUMENT TO ZIP
                |--------------------------------------------------------------------------
                */

                if (
                    !$zip->addFile(
                        $docxPath,
                        $filename
                    )
                ) {

                    throw new RuntimeException(
                        'Unable to add Student Card to ZIP.'
                    );
                }
            }

            $completed = true;

        } finally {

            $closed = $zip->close();

            /*
            |--------------------------------------------------------------------------
            | DELETE TEMPORARY WORD DOCUMENTS
            |--------------------------------------------------------------------------
            */

            foreach ($temporaryDocs as $path) {

                if (is_file($path)) {

                    @unlink($path);
                }
            }

            if (!$completed || !$closed) {

                @unlink($zipPath);
            }
        }

        if (!is_file($zipPath)) {

            throw new RuntimeException(
                'Student Cards ZIP could not be saved.'
            );
        }

        return $zipPath;
    }

    /*
    |--------------------------------------------------------------------------
    | FILL WORD STUDENT CARD TEMPLATE
    |--------------------------------------------------------------------------
    |
    | First Semester: Rows 3-14
    | Second Semester: Rows 18-30
    |
    */

    private function fillWordTemplate(
        string $source,
        string $target,
        array $student,
        array $classroom
    ): void {

        /*
        |--------------------------------------------------------------------------
        | COPY ORIGINAL WORD TEMPLATE
        |--------------------------------------------------------------------------
        */

        if (!copy($source, $target)) {

            throw new RuntimeException(
                'Unable to copy Student Card template.'
            );
        }

        $zip = new ZipArchive();

        if ($zip->open($target) !== true) {

            throw new RuntimeException(
                'Could not open Word Student Card.'
            );
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | READ WORD DOCUMENT XML
            |--------------------------------------------------------------------------
            */

            $xml = $zip->getFromName(
                'word/document.xml'
            );

            if ($xml === false) {

                throw new RuntimeException(
                    'Word document XML is missing.'
                );
            }

            $doc = new DOMDocument(
                '1.0',
                'UTF-8'
            );

            $doc->preserveWhiteSpace = true;

            if (
                !$doc->loadXML(
                    $xml,
                    LIBXML_NONET
                )
            ) {

                throw new RuntimeException(
                    'Unable to read Word document XML.'
                );
            }

            $xp = new DOMXPath($doc);

            $xp->registerNamespace(
                'w',
                self::WORD_NAMESPACE
            );

            /*
            |--------------------------------------------------------------------------
            | FIND ACADEMIC GRADE TABLE
            |--------------------------------------------------------------------------
            */

            $tables = $xp->query(
                '/w:document/w:body/w:tbl'
            );

            if (
                !$tables ||
                $tables->length < 5
            ) {

                throw new RuntimeException(
                    'Word academic table not found.'
                );
            }

            $rows = $xp->query(
                './w:tr',
                $tables->item(4)
            );

            if (
                !$rows ||
                $rows->length < 31
            ) {

                throw new RuntimeException(
                    'Word grade table structure has changed.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT STUDENT PERSONAL INFORMATION
            |--------------------------------------------------------------------------
            */

            $this->rewriteField(
                $doc,
                $xp,
                'Name:',
                'Name: ' . $student['name']
                    . '     Gender: ' . $student['gender']
            );

            $this->rewriteField(
                $doc,
                $xp,
                'Address:',
                'Address: '
            );

            $this->rewriteField(
                $doc,
                $xp,
                'School Year:',
                'School Year: ' . $classroom['year']
                    . '     Grade & Section: '
                    . $classroom['section']
            );

            $this->rewriteField(
                $doc,
                $xp,
                'Strand/Track:',
                'Strand/Track: ' . $classroom['strand']
            );

            $this->rewriteField(
                $doc,
                $xp,
                'LRN:',
                'LRN: '
            );

            /*
            |--------------------------------------------------------------------------
            | INSERT FIRST AND SECOND SEMESTER GRADES
            |--------------------------------------------------------------------------
            */

            foreach (
                self::WORD_ROWS as $semester => $subjectRows
            ) {

                $semesterData =
                    $student['semesters'][$semester] ?? [];

                /*
                |--------------------------------------------------------------------------
                | WRITE EACH SUBJECT'S GRADES
                |--------------------------------------------------------------------------
                */

                foreach ($subjectRows as $code => $rowIndex) {

                    $row = $rows->item($rowIndex);

                    $cells = $xp->query(
                        './w:tc',
                        $row
                    );

                    if (
                        !$cells ||
                        $cells->length < 4
                    ) {

                        throw new RuntimeException(
                            "Word grade cells missing for {$code}."
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | VERIFY SUBJECT NAME IN WORD TABLE
                    |--------------------------------------------------------------------------
                    */

                    $wordCode = $this->subjectCode(
                        $this->cellText(
                            $xp,
                            $cells->item(0)
                        )
                    );

                    if ($wordCode !== $code) {

                        throw new RuntimeException(
                            "Word template row {$rowIndex} is not {$code}."
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | GET STUDENT GRADES
                    |--------------------------------------------------------------------------
                    */

                    $data =
                        $semesterData['grades'][$code] ?? [];

                    /*
                    |--------------------------------------------------------------------------
                    | WRITE QUARTER AND FINAL GRADES
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        ['mid', 'fin', 'ave'] as $offset => $field
                    ) {

                        $value = $data[$field] ?? null;

                        $display = $value === null
                            ? ''
                            : (string) (int) round($value);

                        $this->insertCellValue(
                            $doc,
                            $xp,
                            $cells->item($offset + 1),
                            $display
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | WRITE SEMESTER GENERAL AVERAGE
                |--------------------------------------------------------------------------
                */

                $averageCells = $xp->query(
                    './w:tc',
                    $rows->item(
                        self::AVERAGE_ROWS[$semester]
                    )
                );

                if (
                    !$averageCells ||
                    $averageCells->length < 2
                ) {

                    throw new RuntimeException(
                        'Word semester average row is missing.'
                    );
                }

                $lastCell = $averageCells->item(
                    $averageCells->length - 1
                );

                $this->insertCellValue(
                    $doc,
                    $xp,
                    $lastCell,
                    (string) (
                        $semesterData['general_average'] ?? ''
                    )
                );
            }

            /*
            |--------------------------------------------------------------------------
            | SAVE MODIFIED WORD XML
            |--------------------------------------------------------------------------
            */

            if (
                !$zip->addFromString(
                    'word/document.xml',
                    $doc->saveXML()
                )
            ) {

                throw new RuntimeException(
                    'Unable to save Word Student Card.'
                );
            }

        } finally {

            $zip->close();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | READ WORD CELL TEXT
    |--------------------------------------------------------------------------
    */

    private function cellText(
        DOMXPath $xp,
        DOMElement $cell
    ): string {

        $text = '';

        foreach (
            $xp->query('.//w:t', $cell) as $node
        ) {

            $text .= $node->textContent;
        }

        return trim($text);
    }

    /*
    |--------------------------------------------------------------------------
    | REWRITE STUDENT INFORMATION FIELD
    |--------------------------------------------------------------------------
    */

    private function rewriteField(
        DOMDocument $doc,
        DOMXPath $xp,
        string $prefix,
        string $newText
    ): void {

        $paragraphs = $xp->query(
            '/w:document/w:body/w:p'
        );

        foreach ($paragraphs as $paragraph) {

            $text = '';

            foreach (
                $xp->query('.//w:t', $paragraph) as $node
            ) {

                $text .= $node->textContent;
            }

            if (
                !str_starts_with(
                    trim($text),
                    $prefix
                )
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | PRESERVE ORIGINAL WORD FONT STYLE
            |--------------------------------------------------------------------------
            */

            $firstRun = $xp->query(
                './w:r',
                $paragraph
            )->item(0);

            $runStyle = $firstRun
                ? $xp->query(
                    './w:rPr',
                    $firstRun
                )->item(0)
                : null;

            /*
            |--------------------------------------------------------------------------
            | REMOVE OLD TEXT RUNS
            |--------------------------------------------------------------------------
            */

            $runs = iterator_to_array(
                $xp->query(
                    './w:r',
                    $paragraph
                )
            );

            foreach ($runs as $run) {

                $paragraph->removeChild($run);
            }

            /*
            |--------------------------------------------------------------------------
            | WRITE NEW STUDENT INFORMATION
            |--------------------------------------------------------------------------
            */

            $this->appendTextRun(
                $doc,
                $paragraph,
                $newText,
                $runStyle
            );

            return;
        }

        throw new RuntimeException(
            'Word field not found: ' . $prefix
        );
    }

    /*
    |--------------------------------------------------------------------------
    | INSERT GRADE INTO WORD CELL
    |--------------------------------------------------------------------------
    */

    private function insertCellValue(
        DOMDocument $doc,
        DOMXPath $xp,
        ?DOMElement $cell,
        string $value
    ): void {

        if (!$cell) {

            throw new RuntimeException(
                'Word grade cell was not found.'
            );
        }

        if ($value === '') {
            return;
        }

        $paragraph = $xp->query(
            './w:p',
            $cell
        )->item(0);

        if (!$paragraph) {

            $paragraph = $doc->createElementNS(
                self::WORD_NAMESPACE,
                'w:p'
            );

            $cell->appendChild($paragraph);
        }

        $this->appendTextRun(
            $doc,
            $paragraph,
            $value,
            null
        );
    }

    /*
    |--------------------------------------------------------------------------
    | APPEND WORD TEXT RUN
    |--------------------------------------------------------------------------
    */

    private function appendTextRun(
        DOMDocument $doc,
        DOMElement $paragraph,
        string $text,
        ?DOMElement $style
    ): void {

        $run = $doc->createElementNS(
            self::WORD_NAMESPACE,
            'w:r'
        );

        if ($style) {

            $run->appendChild(
                $style->cloneNode(true)
            );
        }

        $textNode = $doc->createElementNS(
            self::WORD_NAMESPACE,
            'w:t'
        );

        $textNode->setAttribute(
            'xml:space',
            'preserve'
        );

        $textNode->appendChild(
            $doc->createTextNode($text)
        );

        $run->appendChild($textNode);

        $paragraph->appendChild($run);
    }
}
