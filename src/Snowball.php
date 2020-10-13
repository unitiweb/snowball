<?php

namespace Snowball;

//use Snowball\Config;

/**
 * Class Snowball
 */
class Snowball
{
    protected $leftOver = 0;
    protected $debit = 0;
    protected $config = [];
    protected $report;

    protected $data = [
        'stats' => [],
        'bills' => [],
    ];

    public function __construct()
    {
        $this->report = new Report;
    }

    public function addBill(string $name, float $balance, float $payment): void
    {
        $this->data['bills'][] = [
            'name' => $name,
            'balance' => $balance,
            'payment' => $payment,
        ];
    }

    public function setLeftOver(float $leftOver): void
    {
        $this->leftOver = $leftOver;
    }

    /**
     * Calculate the snowball
     *
     * @return void
     */
    public function calculate(): void
    {
        $this->sortBills();

        $totalDebit = 0;
        $freedUp = 0;
        foreach ($this->data['bills'] as $bill) {
            $totalDebit += $bill['balance'];
            $freedUp += $bill['payment'];
        }
        $this->data['stats']['totalDebit'] = $totalDebit;
        $this->data['stats']['freedUp'] = $freedUp;

        $monthNumber = 1;
        $leftOver = $this->leftOver;

        // Loop through each bill
        for ($i = 0; $i < count($this->data['bills']); $i++) {
            $this->debit += $this->data['bills'][$i]['balance'];

            $this->addMonth(
                $monthNumber,
                $leftOver,
                $this->data['bills'][$i]['name'],
                $this->data['bills'][$i]['balance'],
                $this->data['bills'][$i]['payment']
            );

            // Loop months until balance === 0
            while ($this->data['bills'][$i]['balance'] > 0) {

                if ($this->data['bills'][$i]['balance'] >= $leftOver) {
                    // May a payment and reduce the balance
                    $this->data['bills'][$i]['balance'] = $this->data['bills'][$i]['balance'] - $leftOver;
                } else if ($this->data['bills'][$i]['balance'] < $leftOver) {
                    // Payoff the bill
                    $this->data['bills'][$i]['balance'] = 0;
                }

                $this->addMonth(
                    $monthNumber,
                    $leftOver,
                    $this->data['bills'][$i]['name'],
                    $this->data['bills'][$i]['balance'],
                    $this->data['bills'][$i]['payment']
                );

                $monthNumber++;

                // Loop through all bills except the one being paid on and pay it's normal monthly bill
                $this->makePayments($this->data['bills'][$i]);
            }


            // Add paid off bill's payment to the leftOver
            // Since now we have more to pay towards next bill
            $leftOver += $this->data['bills'][$i]['payment'];
        }

        $this->data['stats']['totalMonths'] = $monthNumber;
//        $this->data['stats']['freedUp'] = $leftOver - $this->leftOver;
    }

    protected function makePayments($except)
    {
        for ($i = 0; $i < count($this->data['bills']); $i++) {
            $bill = $this->data['bills'][$i];
            if ($bill['name'] == $except['name'] || $bill['balance'] <= 0) {
                continue;
            }

            // Make a normal payment
            $this->data['bills'][$i]['balance'] = $this->data['bills'][$i]['balance'] - $this->data['bills'][$i]['payment'];
        }
    }

    protected function addMonth($number, $paid, $name, $balance, $payment): void
    {
        $this->data['months'][] = [
            'month' => $number,
            'paid' => $paid,
            'name' => $name,
            'balance' => $balance,
            'payment' => $payment,
        ];
    }

    /**
     * Create report in various output
     *
     * @param string $method
     *
     * @return array|null
     */
    public function report(string $method = 'stdout'): ?array
    {
        $this->report->generate($this->data, 100);

        return null;
    }

    protected function sortBills(): void
    {
        usort($this->data['bills'], function ($a, $b) {
            if ($a['balance'] == $b['balance']) {
                return 0;
            }
            return ($a['balance'] < $b['balance']) ? -1 : 1;
        });
    }
}
