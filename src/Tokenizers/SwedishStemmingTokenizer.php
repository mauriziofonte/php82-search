<?php

namespace MFonte\Search\Tokenizers;

use Wamania\Snowball\Stemmer\Swedish;

class SwedishStemmingTokenizer implements TokenizerInterface
{
    public static function tokenize($data)
    {
        $stemmer = new Swedish();

        return array_map(function ($value) use ($stemmer) {
            return array_unique([$stemmer->stem(mb_convert_encoding($value, 'UTF-8')), $value]);
        }, $data);
    }
}
