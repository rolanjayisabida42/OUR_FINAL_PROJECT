<?php

namespace App\Console\Commands;

use App\Models\Section;
use App\Models\Semester;
use App\Models\SectionSubmission;
use App\Models\SubmissionSetting;
use App\Services\SchoolFormNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendSubmissionDeadlineNotifications extends Command
{
    protected $signature = 'shs:deadline-notifications';

    protected $description =
        'Send Teacher notifications for approaching and missed submission deadlines.';

    public function handle(
        SchoolFormNotifier $notifier
    ): int {

        $today = now('Asia/Manila')->startOfDay();

        $settings = SubmissionSetting::query()
            ->where('status', 'active')
            ->whereNotNull('deadline_at')
            ->get();

        foreach ($settings as $setting) {

            $semester = Semester::find(
                $setting->semester_id
            );

            if (!$semester) {
                continue;
            }

            // Match the deadline's local Philippine date.
            $deadlineDay = Carbon::parse(
                $setting->deadline_at
            )
                ->timezone('Asia/Manila')
                ->startOfDay();

            $daysRemaining = (int) $today->diffInDays(
                $deadlineDay,
                false
            );

            // Send reminders at these milestones only.
            if (!in_array(
                $daysRemaining,
                [7, 3, 1, 0, -1],
                true
            )) {
                continue;
            }

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

                // Respect configured grade scope.
                if (!in_array(
                    (string) $setting->grade_scope,
                    ['both', (string) $section->grade_level],
                    true
                )) {
                    continue;
                }

                $status = SectionSubmission::query()
                    ->where('section_id', $section->id)
                    ->where('semester_id', $semester->id)
                    ->value('status');

                /*
                | Do not remind Teachers whose requirements
                | are already submitted or approved.
                |
                | Draft, missing, and returned submissions
                | can still receive reminders.
                */

                if (in_array(
                    $status,
                    ['submitted', 'approved'],
                    true
                )) {
                    continue;
                }

                $notifier->deadlineReminder(
                    $setting,
                    $section,
                    $daysRemaining
                );
            }
        }

        $this->info(
            'Submission deadline notifications checked.'
        );

        return self::SUCCESS;
    }
}
