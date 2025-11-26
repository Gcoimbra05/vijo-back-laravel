<?php

namespace App\Services;

use App\Models\VijoPlan;

class VijoPlansService {
    public function createVijoPlan(array $data)
    {
        $vijoPlan = VijoPlan::create($data);
        return $vijoPlan;
    }

    public function markCatalogsAsRecorded($user, $vijoPlan)
    {
        $catalogs = $vijoPlan?->catalogs;

        $timezone = $user->timezone ?? config('app.timezone', 'America/New_York');
        $now = now()->setTimezone($timezone);

        foreach ($catalogs as $catalog) {
            $requests = $catalog->videoRequests;
            $freqOfRec = $catalog->frequency_of_recording;
            $numOfRecordedReqs = 0;

            foreach($requests as $request) {
                $result = $this->compareRequestsAndFrequency($now, $freqOfRec, $request->updated_at);
                if ($result) {
                    $numOfRecordedReqs ++;
                }
            }

            if ($numOfRecordedReqs > 0) {
                $catalog->recorded = true;
            } else {
                $catalog->recorded = false;
            }
        }
        return $vijoPlan;
    }

    private function compareRequestsAndFrequency($currentDate, $freqOfRec, $recDate)
    {
        switch ($freqOfRec) {
            case "Daily":
                $dayStart = $currentDate->copy()->startOfDay();
                $dayEnd   = $currentDate->copy()->endOfDay(); 
                $isInCurrentDay = $recDate->between($dayStart, $dayEnd);
                return $isInCurrentDay;

            case "Weekly":
                $weekStart = $currentDate->copy()->startOfWeek();
                $weekEnd = $currentDate->copy()->endOfWeek();
                $isInCurrentWeek = $recDate->between($weekStart, $weekEnd);
                return $isInCurrentWeek;
                
            case "Biweekly":
                $twoWeekStart = $currentDate->copy()->startOfWeek();
                $twoWeekEnd = $twoWeekStart->copy()->addWeeks(2)->subSecond();
                $isInTwoWeeks = $recDate->between($twoWeekStart, $twoWeekEnd);
                return $isInTwoWeeks;

            case "Monthly":
                $monthStart = $currentDate->copy()->startOfMonth();
                $monthEnd = $currentDate->copy()->endOfMonth();
                $isInCurrentMonth = $recDate->between($monthStart, $monthEnd);
                return $isInCurrentMonth;

            case "As inspired":
                return true;

            default:
                return false;
        }
    }
}