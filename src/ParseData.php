<?php

class ParseData
{
    private $headers = ['last', 'first', 'salary'];
    private $data = array();

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function readFile($string)
    {
        try{
            $txt_file = file_get_contents($string);
            foreach (explode("\n", $txt_file) as $line) {
                $row = explode(',', $line);
                $this->data[] = array(
                    $this->headers[0] => $row[0],
                    $this->headers[1] => $row[1],
                    $this->headers[2] => (float)$row[2]
                );
            };
        }
        catch (Exception $e){
            echo 'Message: ' .$e->getMessage();
            throw  new Exception();
        }
    }

    public function getMaxSizeColumn($columnName)
    {
        $maxColumnLen = 0;
        for ($i = 0; $i < count($this->data); $i++){
            $lastLen = strlen($this->data[$i][$columnName]);
            if($lastLen > $maxColumnLen) $maxColumnLen = $lastLen;
        }

        $bonusSpaces = 2;
        return $maxColumnLen + $bonusSpaces;
    }

    private function getMaxSizeColumns(){
        $maxSizeOfColumns = [];
        foreach ($this->headers as $column) {
            $maxSizeOfColumns[] = $this->getMaxSizeColumn($column);
        }

        return $maxSizeOfColumns;
    }

    public function sortBySalary(){
        $countData = count($this->data);
        for($i = 0; $i < $countData - 1; $i++){
            for($j = $i + 1; $j < $countData; $j++){
                if($this->data[$i]['salary'] < $this->data[$j]['salary']){
                    $temp = $this->data[$i];
                    $this->data[$i] = $this->data[$j];
                    $this->data[$j] = $temp;
                }
            }
        }
    }

    public function printData(){
        $cotent = $this->formatDataAsTable();
        echo($cotent);
    }

    public function data($argument1)
    {
        // TODO: write logic here
    }

    /**
     * @return string
     */
    private  function money_format($format, $number)
    {
        $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'.
            '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
                    $match[1] : ' ',
                'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                    $match[0] : '+',
                'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value  *= -1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                    ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                    $csuffix;
            } else {
                $currency = '';
            }
            $space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format($value, $right, $locale['mon_decimal_point'],
                $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                    STR_PAD_RIGHT : STR_PAD_LEFT);
            }

            $format = str_replace($fmatch[0], $value, $format);
        }
        return $locale['currency_symbol'].$format;
    }
    public function moneyFormat(){
        setlocale(LC_MONETARY, 'en_US');
        $countData = count($this->data);
        for($i = 0; $i < $countData; $i++){
            $this->data[$i]['salary']= $this-> money_format('%i',$this->data[$i]['salary']);
        }
    }
    public function formatDataAsTable()
    {
        $size = $this->getMaxSizeColumns();
        $cotent = '';
        $cotent .= 'Last' . str_repeat(' ', $size[0] - strlen($this->headers[0])) .
            'First' . str_repeat(' ', $size[1] - strlen($this->headers[1])) .
            'Salary' . str_repeat(' ', $size[2] - strlen($this->headers[2])) .
            "\n";
        $cotent .= str_repeat('-', array_sum($size)) .
            "\n";
        $countData = count($this->data);

        for ($i = 0; $i < $countData; $i++) {
            $cotent .= $this->data[$i]['last'] . str_repeat(' ', max(0,$size[0] - strlen($this->data[$i]['last']))) .
                $this->data[$i]['first'] . str_repeat(' ', $size[1] - strlen($this->data[$i]['first'])) .
                $this->data[$i]['salary'] . str_repeat(' ', $size[2] - strlen($this->data[$i]['salary'])) . "\n";
        }
        return $cotent;
    }

}
