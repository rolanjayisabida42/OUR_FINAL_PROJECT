<?php

namespace App\Services;

use App\Models\Section;
use App\Models\Semester;
use App\Models\SectionSubmission;
use App\Models\SubmissionSetting;
use App\Models\User;
use App\Notifications\SchoolFormNotice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SchoolFormNotifier
{
    /*
    |--------------------------------------------------------------------------
    | GET TEACHER'S CLASSROOM SUBMISSION URL
    |--------------------------------------------------------------------------
    */

    private function submissionUrl(
        SectionSubmission $submission
    ): string {

        $semester = Semester::find($submission->semester_id);

        return route('teacher.classrooms.submissions', [
            'section' => $submission->section_id,
            'semester' => $semester?->semester_number ?? 1,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TEACHER SUBMITTED REQUIREMENTS
    |--------------------------------------------------------------------------
    |
    | Notify:
    | - Teacher: successful submission
    | - Principal: new submission awaiting review
    |
    */

    public function submitted(
        SectionSubmission $submission,
        bool $resubmitted = false
    ): void {

        $teacher = User::find($submission->teacher_id);

        $section = Section::find($submission->section_id);

        $classroom = $section?->name ?? 'Classroom';

        $url = $this->submissionUrl($submission);

        $date = now('Asia/Manila')
            ->format('M d, Y h:i A');


        if ($teacher) {

            $teacher->notify(new SchoolFormNotice(

                $resubmitted ? 'resubmitted' : 'submitted',

                $resubmitted
                    ? 'Corrections Resubmitted'
                    : 'Requirements Submitted',

                $resubmitted
                    ? "{$classroom}: Your corrected documents were resubmitted on {$date}. Awaiting Principal review."
                    : "{$classroom}: Your requirements were submitted on {$date}. Awaiting Principal review.",

                $url

            ));
        }


        // Notify all active Principal accounts.

        $principals = User::query()
            ->where('role', 'principal')
            ->where('is_active', true)
            ->get();

        foreach ($principals as $principal) {

            $principal->notify(new SchoolFormNotice(

                'principal_submission',

                $resubmitted
                    ? 'Corrected Submission Received'
                    : 'New Submission Received',

                "{$classroom} has submitted requirements for Principal review.",

                route(
                    'principal.submissions.review.show',
                    $submission->id
                )

            ));
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PRINCIPAL APPROVED OR RETURNED REQUIREMENTS
    |--------------------------------------------------------------------------
    */

    public function reviewed(
        SectionSubmission $submission,
        string $decision,
        ?string $remarks = null
    ): void {

        if (!in_array(
            $decision,
            ['approved', 'returned'],
            true
        )) {
            return;
        }

        $teacher = User::find($submission->teacher_id);

        if (!$teacher) {
            return;
        }

        $section = Section::find($submission->section_id);

        $classroom = $section?->name ?? 'Classroom';

        $date = now('Asia/Manila')
            ->format('M d, Y h:i A');

        $url = $this->submissionUrl($submission);

        if ($decision === 'approved') {

            $teacher->notify(new SchoolFormNotice(

                'approved',

                'Requirements Approved',

                "{$classroom}: Your requirements were approved by the Principal on {$date}.",

                $url

            ));

            return;
        }


        $remarksText = trim((string) $remarks);

        $message =
            "{$classroom}: The Principal returned your requirements for correction on {$date}.";

        if ($remarksText !== '') {

            $message .= ' Remarks: ' .
                Str::limit($remarksText, 160);
        }


        $teacher->notify(new SchoolFormNotice(

            'returned',

            'Documents Returned for Correction',

            $message,

            $url

        ));
    }


    /*
    |--------------------------------------------------------------------------
    | SUBMISSION PERIOD OPENED / DEADLINE UPDATED
    |--------------------------------------------------------------------------
    |
    | Call this after the Principal successfully
    | saves the corresponding submission setting.
    |
    */

    public function periodUpdated(
        SubmissionSetting $setting,
        string $event
    ): void {

        if (!in_array(
            $event,
            ['opened', 'deadline_updated'],
            true
        )) {
            return;
        }

        $semester = Semester::find($setting->semester_id);

        if (!$semester) {
            return;
        }

        $deadline = $setting->deadline_at
            ? Carbon::parse($setting->deadline_at)
                ->timezone('Asia/Manila')
                ->format('M d, Y h:i A')
            : 'Not yet set';

        $sections = Section::query()
            ->with(['adviser', 'schoolYear', 'strand'])
            ->where('school_year_id', $semester->school_year_id)
            ->where('is_active', true)
            ->get();

        foreach ($sections as $section) {

            if (
                !$section->schoolYear?->is_active ||
                !$section->strand?->is_active
            ) {
                continue;
            }

            if (!in_array(
                (string) $setting->grade_scope,
                ['both', (string) $section->grade_level],
                true
            )) {
                continue;
            }

            $teacher = User::query()
                ->whereKey($section->adviser?->teacher_id)
                ->where('role', 'teacher')
                ->where('is_active', true)
                ->first();

            if (!$teacher) {
                continue;
            }

            $url = route('teacher.classrooms.submissions', [
                'section' => $section->id,
                'semester' => $semester->semester_number,
            ]);

            $teacher->notify(new SchoolFormNotice(

                'period',

                $event === 'opened'
                    ? 'Submission Period Open'
                    : 'Submission Deadline Updated',

                $event === 'opened'
                    ? "{$section->name}: The submission period is now open. Deadline: {$deadline}."
                    : "{$section->name}: The Principal updated your submission deadline to {$deadline}.",

                $url

            ));
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DEADLINE REMINDERS
    |--------------------------------------------------------------------------
    |
    | $days:
    |   7, 3, 1 = approaching
    |   0       = due today
    |  -1       = overdue
    |
    */

    public function deadlineReminder(
        SubmissionSetting $setting,
        Section $section,
        int $days
    ): void {

        $teacher = User::query()
            ->whereKey($section->adviser?->teacher_id)
            ->where('role', 'teacher')
            ->where('is_active', true)
            ->first();

        if (!$teacher) {
            return;
        }

        $semester = Semester::find($setting->semester_id);

        if (!$semester || !$setting->deadline_at) {
            return;
        }

        $deadline = Carbon::parse($setting->deadline_at)
            ->timezone('Asia/Manila')
            ->format('M d, Y h:i A');

        /*
        | Unique event key prevents duplicates
        | when the command runs more than once.
        */

        $key = implode(':', [
            'deadline',
            $setting->id,
            $section->id,
            $days,
            (string) $setting->deadline_at,
        ]);

        if (
            $teacher->notifications()
                ->where('data->key', $key)
                ->exists()
        ) {
            return;
        }

        $title = match ($days) {
            7 => 'Deadline in 7 Days',
            3 => 'Deadline in 3 Days',
            1 => 'Deadline Tomorrow',
            0 => 'Submission Due Today',
            default => 'Submission Overdue',
        };

        $message = $days < 0
            ? "{$section->name}: Your submission deadline passed on {$deadline}. Contact the Principal if an extension is needed."
            : "{$section->name}: Your requirements must be submitted by {$deadline}.";

        $url = route('teacher.classrooms.submissions', [
            'section' => $section->id,
            'semester' => $semester->semester_number,
        ]);

        $teacher->notify(new SchoolFormNotice(

            $days < 0 ? 'overdue' : 'deadline',

            $title,

            $message,

            $url,

            $key

        ));
    }
}
