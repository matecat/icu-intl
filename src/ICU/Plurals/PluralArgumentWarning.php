<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 06/02/26
 * Time: 12:00
 *
 */

namespace Matecat\ICU\Plurals;

use Matecat\ICU\Tokens\ArgType;
use Stringable;

/**
 * Represents compliance warnings for a single plural/selectordinal argument.
 *
 * Each instance contains information about issues found in one specific
 * plural or selectordinal block within a message pattern.
 */
readonly class PluralArgumentWarning implements Stringable
{
    /**
     * @param string $argumentName The name of the argument (e.g., 'count', 'num_guests').
     * @param ArgType $argumentType The type of the argument (PLURAL or SELECTORDINAL).
     * @param array<string> $expectedCategories The valid CLDR categories for this argument type and locale.
     * @param array<string> $foundSelectors All selectors found in this argument.
     * @param array<string> $missingCategories Expected categories not found in this argument.
     * @param array<string> $numericSelectors Explicit numeric selectors found (e.g., =0, =1, =2).
     * @param array<string> $wrongLocaleSelectors Valid CLDR categories that don't apply to this locale/type.
     */
    public function __construct(
        public string $argumentName,
        public ArgType $argumentType,
        public array $expectedCategories,
        public array $foundSelectors,
        public array $missingCategories,
        public array $numericSelectors,
        public array $wrongLocaleSelectors,
        public string $locale
    ) {
    }

    /**
     * Get the argument type as a human-readable string.
     */
    public function getArgumentTypeLabel(): string
    {
        return $this->argumentType === ArgType::SELECTORDINAL ? 'selectordinal' : 'plural';
    }

    /**
     * Generates a human-readable warning message for this argument.
     */
    public function getMessageAsString(): string
    {
        return (string)$this;
    }

    /**
     * @return array<string>
     */
    public function getMessages(): array
    {
        $messages = [];
        $typeLabel = $this->getArgumentTypeLabel();

        if (!empty($this->wrongLocaleSelectors)) {
            $messages[] = sprintf(
                '%s argument "%s": Categories [%s] are valid CLDR categories but do not apply to the locale \'%s\'. '
                . 'Expected categories: [%s].',
                ucfirst($typeLabel),
                $this->argumentName,
                implode(', ', $this->wrongLocaleSelectors),
                $this->locale,
                implode(', ', $this->expectedCategories),
            );
        }

        if (!empty($this->missingCategories)) {
            $messages[] = sprintf(
                '%s argument "%s": Missing required categories [%s] in plural block for the locale \'%s\'. '
                . 'Expected categories: [%s].',
                ucfirst($typeLabel),
                $this->argumentName,
                implode(', ', $this->missingCategories),
                $this->locale,
                implode(', ', $this->expectedCategories),
            );
        }

        return $messages;
    }

    public function __toString(): string
    {
        return implode(' ', $this->getMessages());
    }
}
