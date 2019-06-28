<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class UserTechnologyMappingTransformer extends TransformerAbstract {

    public function transform(\App\UserTechnologyMapping $table) {
        $duration = $this->calculateDurationInMonthsAndYears($table->duration_in_month);
        return [
            'id' => $table->id,
            'technology_id' => $table->technology_id,
            'duration_in_month_old' => $table->duration_in_month,
            'duration_months' => $duration["month"],
            'duration_years' => $duration["years"],
            'technology' => $table->technology,
            'domains' => $table->domain,
            'user' => $table->user,
            'updated' => $table->updated_data,
            'duration' => $table->duration,
        ];
        
    }
    
    // Calculate duration in months from year & month
    function calculateDurationInMonthsAndYears($months) {
        $duration = [];
        $years = floor($months/12);
        $months = $months%12;
        $duration["month"] = $months;
        $duration["years"] = $years;
        return $duration;
    }
    
    // floor($months/12) . ' years and ' . $months%12 . ' months'
}
