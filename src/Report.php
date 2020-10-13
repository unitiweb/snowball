<?php

namespace Snowball;

/**
 * Class Report
 * @package Snowball
 */
class Report
{
    /**
     * The max line width
     * @var int
     */
    protected $width = 113;

    /**
     * The max month width
     * @var int
     */
    protected $monthWidth = 25;

    /**
     * The max name width
     * @var int
     */
    protected $nameWidth = 30;

    /**
     * The max paid width
     * @var int
     */
    protected $paidWidth = 10;

    /**
     * The max balance width
     * @var int
     */
    protected $balanceWidth = 14;

    /**
     * Generate the report
     *
     * @param array $data The months array
     *
     * @return void
     */
    public function generate(array $data): void
    {
//        $width = $this->monthWidth + $this->nameWidth + $this->paidWidth + $this->balanceWidth;
//        $this->width = $width;

        $stats = $data['stats'] ?? [];
        $months = $data['months'] ?? [];

        // Pad the top to remove old results
        echo str_repeat("\n", 130);

        $this->header($stats, $months);

        $start = true;
        $subCount = 0;
        foreach ($months as $month) {
            if ($start) {
                $this->blank();
                $this->line($this->currency($month['paid']));
                $subCount = 0;
            }

            $this->month($month, $subCount);

            $subCount++;

            if ($start) {
                $this->line(null, '=');
            }

            $start = false;
            if ($month['balance'] <= 0) {
                $start = true;
            }
        }
    }

    public function getCounts(array $months): array
    {

        foreach ($months as $month) {

        }
    }

    public function header(array $stats, array $months): void
    {
        $pad = 15;
        $debit = str_pad($this->currency($stats['totalDebit']), $pad, ' ', STR_PAD_LEFT);
        $total = str_pad($stats['totalMonths'] . ' months', $pad, ' ', STR_PAD_LEFT);
        $freedUp = str_pad($this->currency($stats['freedUp']), $pad, ' ', STR_PAD_LEFT);

        $this->line();
        echo str_pad(' Snowball Report', $pad) . "\n";

        echo str_pad("   Total Debit:", $pad) . $debit . "\n";
        echo str_pad("   Paid off in:", $pad) . $total . "\n";
        echo str_pad("   Freed Up:", $pad) . $freedUp . "\n";


//        echo "   Total Debit: {$debit}" . "\n";
//        echo "   Paid off in: {$total}" . "\n";
//        echo "   Freed Up: {$freedUp}" . "\n";
        $this->line();
    }

    /**
     * Create a blank line
     *
     * @return void
     */
    public function blank(): void
    {
        echo "\n";
    }

    /**
     * Make a currency string
     *
     * @param float|null $value The number to convert
     *
     * @return string
     */
    public function currency(?float $value): string
    {
        return '$' . number_format($value, 2);
    }

    /**
     * Make a month line
     *
     * @param array $data
     * @param int $count
     * @param int $subCount
     *
     * @return void
     */
    public function month(array $data, int $subCount = 0): void
    {
        $date = $data['date']->format('F Y');
        $month = $data['month'] ?? '';

        if ($subCount === 0) {
            $month = str_pad('Starting', $this->monthWidth);
        } else {
            $month = str_pad('#' . $month . ': ' . $date, $this->monthWidth);
        }

        $name = $data['name'] ?? '';
        $paid = $data['paid'] ?? '';
        $balance = $data['balance'] ?? '';

//        $month['month'] = $month['month'] . ' (' . $subCount . ')';

        $name = str_pad($name, $this->nameWidth);
        $paid = str_pad($this->currency($paid), $this->paidWidth, ' ', STR_PAD_LEFT);
        $balance = str_pad($this->currency($balance), $this->balanceWidth, ' ', STR_PAD_LEFT);

        echo "|-- $month: $name: Paid $paid bringing balance to $balance\n";
    }

    /**
     * Echo out a line with optional prefix text
     *
     * @param string|null $prefix
     * @param string $repeater
     *
     * @return void
     */
    public function line(string $prefix = null, string $repeater = '-'): void
    {
        // No Prefix
        if (strlen($prefix) === 0) {
            echo str_repeat($repeater, $this->width) . "\n";
        } else {
            // With Prefix
            $length = $this->width - strlen($prefix) - 4;

            echo '-- ' . $prefix . ' ' . str_repeat('-', $length) . "\n";
        }
    }
}
