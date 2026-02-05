<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 05/02/26
 * Time: 10:30
 *
 */

namespace Matecat\ICU;

/**
 * Exception thrown when a message pattern's plural selectors do not comply with
 * the expected CLDR plural categories for a given locale.
 *
 * This exception is raised by MessagePatternAnalyzer::validatePluralCompliance()
 * when the message contains invalid plural selectors or missing required categories.
 */
class PluralComplianceException extends \Exception
{
    /**
     * @param array<string> $expectedCategories The valid CLDR categories for this locale.
     * @param array<string> $foundSelectors All selectors found in the message.
     * @param array<string> $invalidSelectors Selectors that don't match expected categories.
     * @param array<string> $missingCategories Expected categories not found in the message.
     * @param bool $hasComplexPluralForm Whether the message contains plural/selectordinal forms.
     * @param int $code
     * @param ?\Throwable $previous
     */
    public function __construct(
        public readonly array $expectedCategories,
        public readonly array $foundSelectors,
        public readonly array $invalidSelectors,
        public readonly array $missingCategories,
        public readonly bool $hasComplexPluralForm,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $message = $this->generateMessage();
        parent::__construct($message, $code, $previous);
    }

    /**
     * Generates a human-readable error message from the compliance details.
     */
    private function generateMessage(): string
    {
        if (!$this->hasComplexPluralForm) {
            return 'No plural forms found in the message.';
        }

        $messages = [];

        if (!empty($this->invalidSelectors)) {
            $messages[] = sprintf(
                'Invalid selectors found: [%s]. Expected categories: [%s].',
                implode(', ', $this->invalidSelectors),
                implode(', ', $this->expectedCategories)
            );
        }

        if (!empty($this->missingCategories)) {
            $messages[] = sprintf(
                'Missing categories: [%s].',
                implode(', ', $this->missingCategories)
            );
        }

        return implode(' ', $messages) ?: 'Plural compliance validation failed.';
    }

    /**
     * Returns true if the 'other' category is missing.
     * The 'other' category is recommended for all plural forms as a fallback.
     */
    public function isMissingOther(): bool
    {
        return in_array('other', $this->missingCategories, true);
    }
}
