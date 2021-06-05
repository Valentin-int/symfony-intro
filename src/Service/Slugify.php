<?php

namespace App\Service;

class Slugify
{
    public const SPECIAL_CARACTERE = [
        "/[àâ]/u" => 'a',
        "/[ÀÂ]/u" => 'a',
        "/[ç]/u" => 'c',
        "/[Ç]/u" => 'c',
        "/[éèêë]/u" => 'e',
        "/[ÉÈÊË]/u" => 'e',
        "/[îï]/u" => 'i',
        "/[ÎÏ]/u" => 'i',
        "/[ô]/u" => 'o',
        "/[Ô]/u" => 'o',
        "/[ùûü]/u" => 'u',
        "/[ÙÛÜ]/u" => 'u',
        "/[ÿ]/u" => 'y',
        "/[Ÿ]/u" => 'y',
        "/[!\"\'\`\$*+_,;:?]/u" => '',
        "/[-]{2,}/u" => '-',
    ];

    public function generate(string $input) : string
    {
        $removeSpace = explode(" ", $input);
        $correctionWord = preg_replace(array_keys(self::SPECIAL_CARACTERE), array_values(self::SPECIAL_CARACTERE), $removeSpace);
        $addTreatyInWord = implode("-", $correctionWord);
        $correctionSentence = trim(strtolower($addTreatyInWord));
        return $correctionSentence;
    }
}
