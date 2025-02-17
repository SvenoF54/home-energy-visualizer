<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

enum TimePeriodEnum: string
{
    case Today = 'today';
    case ThisWeek = 'this_week';
    case ThisMonth = 'this_month';
    case ThisYear = 'this_year';

    public function getStartDate(): string
    {
        return match ($this) {
            self::Today => date('Y-m-d 00:00:00'),
            self::ThisWeek => date('Y-m-d 00:00:00', strtotime('monday this week')),
            self::ThisMonth => date('Y-m-01 00:00:00'),
            self::ThisYear => date('Y-01-01 00:00:00'),
        };
    }

    public function getEndDate(): string
    {
        return match ($this) {
            self::Today => date('Y-m-d 23:59:59'),
            self::ThisWeek => date('Y-m-d 23:59:59', strtotime('sunday this week')),
            self::ThisMonth => date('Y-m-t 23:59:59'),
            self::ThisYear => date('Y-12-31 23:59:59'),
        };
    }

    public function label(): string
    {
        return match($this) {
            static::Today => 'Heute',
            static::ThisWeek => 'Diese Woche',
            static::ThisMonth => 'Dieser Monat',
            static::ThisYear => 'Dieses Jahr'
        };
    }
}
