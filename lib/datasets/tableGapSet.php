<?php
class TableGapSet
{
    private $gapStart;
    private $gapEnd;
    private int $gapDurationInSeconds;

    public function __construct($gapStart, $gapEnd, int $gapDurationInSeconds)
    {
        $this->gapStart = $gapStart;
        $this->gapEnd = $gapEnd;
        $this->gapDurationInSeconds = $gapDurationInSeconds;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['gapStart'],
            $data['gapEnd'],
            (int)$data['gapDurationInSeconds']
        );
    }

    public function getGapStart()
    {
        return $this->gapStart;
    }

    public function getGapEnd()
    {
        return $this->gapEnd;
    }

    public function getGapDurationInSeconds(): int
    {
        return $this->gapDurationInSeconds;
    }

    public static function groupByMonth(array $gaps) {
        $groupedGaps = [];
        foreach ($gaps as $gapSet) {
            $month = date("Y-m", strtotime($gapSet->getGapStart()));
            $groupedGaps[$month][] = $gapSet;
        }        

        return $groupedGaps;
    }
}
