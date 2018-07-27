<?php
require_once ('ParseData.php');
/**
 * Created by PhpStorm.
 * User: toanlamt
 * Date: 7/26/2018
 * Time: 11:39 AM
 */


$parsedata = new ParseData();
$filename="data.txt";

$parsedata->readFile($filename);
$parsedata->sortBySalary();
$parsedata->moneyFormat();
$parsedata->printData();