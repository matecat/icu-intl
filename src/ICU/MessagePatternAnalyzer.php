<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 04/02/26
 * Time: 17:09
 *
 */

namespace Matecat\ICU;

class MessagePatternAnalyzer
{

    public function __construct(
        protected MessagePattern $pattern,
        protected string $language = 'en-US'
    ) {
    }

    /**
     * @return bool Returns true if the message pattern contains complex syntax (plural, select, choice, selectordinal),
     * false otherwise.
     */
    public function containsComplexSyntax(): bool
    {
        $complex = false;
        foreach ($this->pattern as $part) {
            $argType = $part->getArgType();
            $complex |= $argType->hasPluralStyle() ||
                $argType === ArgType::SELECT ||
                $argType === ArgType::CHOICE;
        }

        return (bool)$complex;
    }

}