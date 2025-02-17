<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class TimeHelper {

    public static function convertSecondsToLabel($val, $prefixSingle="", $prefixMultiple="") {
        if ($val >= 86400) {
            $day = $val / 86400;                        
            return ($day == 1 ? "$prefixSingle " : "$prefixMultiple ") 
                   . $day . ($day == 1 ? " Tag" : " Tage");            
        } elseif ($val >= 3600) {
            $hours = $val / 3600;
            return ($hours == 1 ? "$prefixSingle " : "$prefixMultiple ") 
                   . $hours . ($hours == 1 ? " Stunde" : " Stunden");            
        } elseif ($val >= 60) {
            $minutes = ($val / 60);
            return ($minutes == 1 ? "$prefixSingle " : "$prefixMultiple ") 
                    . $minutes . ($minutes == 1 ? " Minute" : " Minuten");
        }
        return $val . ($val == 1 ? "Sekunde" : " Sekunden");            
    }

    public static function formatDate($mysqlDate)
    {
        return date("d.m.Y", strtotime($mysqlDate));
    }

    public static function formatMonthNameAndYear($mysqlDate, $monthNameShort = true)
    {
        $monthName = MONTH_LIST[date("n", strtotime($mysqlDate))];
        $monthName = $monthNameShort ? substr($monthName, 0, 3) : $monthName;
        return $monthName." ".date("Y", strtotime($mysqlDate));
    }

    public static function formatDateTime($mysqlDate, $withSeconds = false)
    {        
        return $withSeconds ? date("d.m.Y H:i:s", strtotime($mysqlDate)) : date("d.m.Y H:i", strtotime($mysqlDate));
    }

    public static function formatForDatabase($dateTime, $withTime = true) {
        return $withTime ? date("Y-m-d H:i:s", strtotime($dateTime)) : date("Y-m-d", strtotime($dateTime));
    }

    public static function prepareTimeUnit($startTime, $endTime)
    {
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);
        $timeDiff = $endTimestamp - $startTimestamp;

        if ($timeDiff <= 60) {
            return 'second'; 
        } elseif ($timeDiff <= 3600) {
            return 'minute'; 
        } elseif ($timeDiff <= 86400) {
            return 'hour'; 
        } elseif ($timeDiff <= 2678400) {
            return 'day'; 
        } elseif ($timeDiff <= 31536000) {
            return 'month';
        } else {
            return 'year'; 
        }        
    }    

    public static function getDaysInMonth($month, $year) {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    public static function calculateDifferenceInDays($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $difference = $start->diff($end);
    
        return $difference->days; // difference in days
    }

    public static function getWeekday($date, $short = false) {
        $formatter = new IntlDateFormatter(
            setlocale(LC_TIME, 0), // Locale for german
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $formatter->setPattern('EEEE'); // pattern weekday
        $result = $formatter->format(new DateTime($date));
        return $short ? substr($result,0,2)."." : $result;
    }    

    public static function getQuarterHoursBetween($start, $end) {
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
    
        $interval = $startDate->diff($endDate);
    
        $totalMinutes = ($interval->y * 525600) + // Years in minutes
                        ($interval->m * 43200) +  // months in minutes
                        ($interval->d * 1440) +   // days in minutes
                        ($interval->h * 60) +     // hours in minutes
                        $interval->i;             // minutes
    
        // Divide with 15 to get the count of querter hours
        $quarterHours = floor($totalMinutes / 15);
    
        return $quarterHours;
    }    
    
    public static function getEndOfMonth($date, $withTime = false) {
        $dateTime = new DateTime($date);        
        $dateTime->modify('first day of next month');        
        $dateTime->modify('-1 day');
        
        return $withTime ? $dateTime->format('d.m.Y')." 23:59:59" : $dateTime->format('d.m.Y');
    }

    public static function getEndOfDay($date, $withTime = false) {
        $dateTime = new DateTime($date);        
        
        return $withTime ? $dateTime->format('d.m.Y')." 23:59:59" : $dateTime->format('d.m.Y');
    }

    public static function formatSeconds($seconds): string
    {
        $seconds = (int) $seconds;
        if ($seconds < 0) {
            return "Invalid input";
        }
    
        $days = intdiv($seconds, 86400); // 86400 Sekunden in einem Tag
        $seconds %= 86400;
    
        $hours = intdiv($seconds, 3600); // 3600 Sekunden in einer Stunde
        $seconds %= 3600;
    
        $minutes = intdiv($seconds, 60); // 60 Sekunden in einer Minute
        $seconds %= 60;
    
        $formatted = [];
    
        if ($days > 0) {
            $formatted[] = "$days Tag" . ($days > 1 ? "e" : "");
        }
    
        if ($hours > 0) {
            $formatted[] = "$hours Stunde" . ($hours > 1 ? "n" : "");
        }
    
        if ($minutes > 0) {
            $formatted[] = "$minutes Minute" . ($minutes > 1 ? "n" : "");
        }
    
        if ($seconds > 0 || empty($formatted)) {
            $formatted[] = "$seconds Sekunde" . ($seconds > 1 ? "n" : "");
        }
    
        return implode(", ", $formatted);
    }
    }
