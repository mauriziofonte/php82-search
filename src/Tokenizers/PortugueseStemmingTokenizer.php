<?php

namespace MFonte\Search\Tokenizers;

use Wamania\Snowball\Stemmer\Portuguese;

class PortugueseStemmingTokenizer implements TokenizerInterface
{
    public static function tokenize($data)
    {
        $stemmer = new Portuguese();

        return array_map(function ($value) use ($stemmer) {
            return array_unique([$stemmer->stem(mb_convert_encoding($value, 'UTF-8')), $value]);
        }, $data);
    }
}
