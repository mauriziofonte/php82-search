<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 15/06/2018
 * Time: 12:50.
 */

namespace MFonte\Search\Tokenizers;

class TrimPunctuationTokenizer implements TokenizerInterface
{
    public static function tokenize($data)
    {
        return array_map(function ($elem) {
            return trim($elem, ",?;.:!()/\\-_'\""); // TODO : preg_replace
        }, $data);
    }
}
