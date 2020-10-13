<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Snowball\Snowball;

$snowball = new Snowball;
$snowball->setLeftOver(500);

$snowball->addBill('Amazon Rewards Card', 1047, 40);
$snowball->addBill('Capital One 7902', 2360, 60);
$snowball->addBill('Capital One 8385', 5787, 280);
$snowball->addBill('Home Depot', 1396, 50);
$snowball->addBill('Mountain America CC', 4596, 120);
$snowball->addBill('Mountain America Equity', 13313, 50);
$snowball->addBill('Apple Card', 5476, 300);
$snowball->addBill('Kia Loan', 4173, 331);
$snowball->addBill('Sea Ray Loan', 3450, 200);

$snowball->calculate();
$snowball->report();

