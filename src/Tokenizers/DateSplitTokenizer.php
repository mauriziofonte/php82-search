<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 15/06/2018
 * Time: 09:12.
 */

namespace Mfonte\Search\Tokenizers;

class DateSplitTokenizer implements TokenizerInterface
{
    public static function tokenize($data)
    {
        return array_map(function ($date) {
            return [$date, mb_substr($date, 0, 10), mb_substr($date, 11, 8)];
        }, $data);
    }
}
