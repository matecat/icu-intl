<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 06/02/26
 * Time: 10:30
 *
 */

namespace Matecat\ICU\Plurals;

use Stringable;

/**
 * Warning object returned when a message pattern's plural selectors have compliance issues
 * that don't warrant an exception.
 *
 * This is returned when:
 * - Valid CLDR categories are used that don't apply to the locale (e.g., 'few' in English)
 * - Required categories are missing
 *
 * The pattern is still syntactically valid, but may not provide proper localization coverage.
 *
 * Note: When a pattern has truly invalid selectors (not valid CLDR categories at all),
 * a PluralComplianceException is thrown instead.
 */
readonly class PluralComplianceWarning implements Stringable
{
    /**
     * @param array<PluralArgumentWarning> $argumentWarnings Warnings for each argument with issues.
     */
    public function __construct(
        public array $argumentWarnings
    ) {
    }

    /**
     * Get all argument warnings.
     *
     * @return array<PluralArgumentWarning>
     */
    public function getArgumentWarnings(): array
    {
        return $this->argumentWarnings;
    }

    /**
     * Get all missing categories across all arguments.
     *
     * @return array<string>
     */
    public function getAllMissingCategories(): array
    {
        $missing = [];
        foreach ($this->argumentWarnings as $warning) {
            $missing = array_merge($missing, $warning->missingCategories);
        }
        return array_unique($missing);
    }

    /**
     * Get all wrong locale selectors across all arguments.
     *
     * @return array<string>
     */
    public function getAllWrongLocaleSelectors(): array
    {
        $wrong = [];
        foreach ($this->argumentWarnings as $warning) {
            $wrong = array_merge($wrong, $warning->wrongLocaleSelectors);
        }
        return array_unique($wrong);
    }

    /**
     * Get all warning messages as an array.
     *
     * @return array<string>
     */
    public function getMessages(): array
    {
        $messages = [];
        foreach ($this->argumentWarnings as $warning) {
            $message = $warning->getMessageAsString();
            if (!empty($message)) {
                $messages[] = $message;
            }
        }
        return $messages;
    }

    /**
     * Generates a human-readable warning message.
     */
    public function getMessagesAsString(): string
    {
        return (string)$this;
    }

    public function __toString(): string
    {
        return implode("\n", $this->getMessages()) ?: 'Plural compliance warning.';
    }
}
