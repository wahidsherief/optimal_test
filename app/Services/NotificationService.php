<?php

namespace App\Services;

use App\Events\NotifyEvent;
use App\Models\Candidate;
use App\Models\JobAlert;
use App\Models\QamlaJob;
use App\Notifications\BaseNotification;

class NotificationService
{
    public function notify($notifiable, array $data, array $options)
    {
        try {
            $sendNotification = $options['sendNotification'] ?? false;
            $sendEmail = $options['sendEmail'] ?? false;
            $emailTemplate = $options['emailTemplate'] ?? null;
            $channel = $options['channel'] ?? 'default';
            $success = false;

            // Handle email notification
            if ($sendEmail && $emailTemplate) {
                defer(fn() => $notifiable->notify(new BaseNotification($data, [
                    'sendEmail' => true,
                    'emailTemplate' => 'emails.' . $emailTemplate
                ])));
                $success = true;
            }

            // Handle database notification and event
            if ($sendNotification) {
                // Send database notification
                defer(fn() => $notifiable->notify(new BaseNotification($data, [
                    'sendEmail' => false
                ])));

                // Wait for notification to be saved then dispatch event
                defer(function () use ($notifiable, $data, $channel) {
                    $notification = $notifiable->notifications()
                        ->latest()
                        ->first();

                    if ($notification) {
                        $notificationData = [
                            'id' => $notification->id,
                            'read_at' => $notification->read_at,
                        ];

                        $mergedData = array_merge($data, $notificationData);
                        event(new NotifyEvent($mergedData, $channel));
                    } else {
                        info('Failed to fetch the notification from the database.');
                    }
                });

                $success = true;
            }

            return $success;
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function sendInstantJobAlerts(QamlaJob $job)
    {
        $candidateIds = JobAlert::where(function ($query) use ($job) {
            $query->where('industry_id', $job->industry_id)
                ->orWhereRaw('LOWER(job_title) = ?', [strtolower($job->title)])
                ->orWhere('location_id', $job->employer->company->location_id)
                ->orWhere('workplace_id', $job->workplace_id);
        })
            ->pluck('candidate_id');


        if ($candidateIds->isEmpty()) {
            return false;
        }

        $company = $job->employer->company;
        $locationName = $company->location->name;
        $workplaceName = $job->workplace->name;
        $salary = $job->min_salary && $job->max_salary ? '£' . $job->min_salary . ' - ' . '£' . $job->max_salary : 'Negotiable';
        $jobUrl = appURL() . '/jobs/' . $job->id;

        foreach ($candidateIds as $candidateId) {
            $candidate = Candidate::find($candidateId);

            if ($candidate) {
                $data = [
                    'jobTitle' => $job->title,
                    'companyName' => $company->name,
                    'location' => $locationName,
                    'workplace' => $workplaceName,
                    'salary' => $job->salary,
                    'companyLogoUrl' => $company->image,
                    'salary' => $salary,
                    'url' => $jobUrl,
                    'title' => 'Qamla Job Alert: ' . $job->title,
                    'message' => "A new job that matches your preferences has been posted!",
                ];

                $options = [
                    'sendNotification' => true,
                    'sendEmail' => true,
                    'emailTemplate' => 'job_alert',
                    'channel' => 'candidate.' . $candidate->id
                ];

                $this->notify($candidate, $data, $options);
            }
        }
    }
}
