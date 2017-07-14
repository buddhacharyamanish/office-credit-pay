<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Maatwebsite\Excel\Excel;

class ParserController extends Controller
{
    /**
     * @var Excel
     */
    protected $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function index()
    {
        return view('demo');
    }

    public function upload(Request $request)
    {
        $file = $request->file('demo');

        $contents = $this->excel->load($file)->toArray();
        $data     = [];
        $rows     = count($contents);

        $header = "RCTP01  000000000001000000000000000001 FTYPIBI %s 1001                                                                                                                                                                                                                                                                                                                                                                                                                                                ";

        $template = "RCTP03  %s                                         %sCR524 %s         CMTP723  12                                                          %s                                                                                                                                                                                                                                                                                                                         ";
        foreach ($contents as $index => $content) {
            $serialNo = $index + 2;
            $digits   = strlen((string) $serialNo);
            $date     = date('mdY');
            $amount   = $content['amount'];

            if ($digits < 12) {
                $diff = 12 - $digits;
                $serialNo = str_repeat('0', $diff) . $serialNo;
            }

            if (count($amount) < 16) {
                $amountDiff = 16 - strlen((string) $amount);
                $amount = str_repeat('0', $amountDiff) . $amount;
            }

            $data[] = sprintf($template, $serialNo, $amount, $date, (string) $content['card_number']);
        }

        $footer = "RCTP02  %sLR00000000                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ";
        $lastNumber = count($data) + 2;
        $lastDigits = strlen((string) $lastNumber);

        if ($lastDigits < 12)  {
            $diff = 12 - $lastDigits;
            $lastNumber = str_repeat('0', $diff) . $lastNumber;
        }

        $header = sprintf($header, date('mdYHis'));
        $footer = sprintf($footer, $lastNumber);

        array_unshift($data, $header);
        $data[] = $footer;

        $result = implode("\n", $data);

        file_put_contents('result.txt', $result);

        dd('Done');
    }
}
