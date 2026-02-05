<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 04/02/26
 * Time: 17:09
 *
 */

namespace Matecat\ICU;

use Matecat\ICU\Parts\TokenType;
use Matecat\ICU\PluralRules\PluralRules;

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

    /**
     * Validates whether the plural/selectordinal forms in the message pattern comply
     * with the expected CLDR plural categories for the configured locale.
     *
     * This method extracts all selectors from plural and selectordinal arguments
     * and checks if they match the valid categories for the language.
     *
     * Valid selectors include:
     * - CLDR category names: 'zero', 'one', 'two', 'few', 'many', 'other'
     * - Explicit numeric selectors: '=0', '=1', '=2', etc.
     *
     * @throws PluralComplianceException If any selector is invalid or required categories are missing.
     *
     * @return void
     */
    public function validatePluralCompliance(): void
    {
        $expectedCategories = PluralRules::getCategories($this->language);
        $foundSelectors = [];
        $invalidSelectors = [];
        $hasComplexPluralForm = false;

        foreach ($this->pattern as $part) {
            $argType = $part->getArgType();

            // Only check plural and selectordinal arguments
            if (!$argType->hasPluralStyle()) {
                continue;
            }

            $hasComplexPluralForm = true;

            // Find all selectors within this argument
            $selectors = $this->extractSelectorsForArgument($part);

            foreach ($selectors as $selector) {
                $foundSelectors[] = $selector;

                // Explicit numeric selectors (=0, =1, =2, etc.) are always valid
                if (preg_match('/^=\d+$/', $selector)) {
                    continue;
                }

                // Check if the selector is a valid CLDR category for this locale
                if (!in_array($selector, $expectedCategories, true)) {
                    $invalidSelectors[] = $selector;
                }
            }
        }

        // Find missing categories (categories expected but not found)
        $foundCategorySelectors = array_filter($foundSelectors, fn($s) => !preg_match('/^=\d+$/', $s));
        $missingCategories = array_values(array_diff($expectedCategories, $foundCategorySelectors));

        // Only throw exception if there are actual plural/selectordinal forms AND issues
        if ($hasComplexPluralForm && (!empty($invalidSelectors) || !empty($missingCategories))) {
            throw new PluralComplianceException(
                expectedCategories: $expectedCategories,
                foundSelectors: array_unique($foundSelectors),
                invalidSelectors: array_unique($invalidSelectors),
                missingCategories: $missingCategories,
                hasComplexPluralForm: $hasComplexPluralForm
            );
        }
    }

    /**
     * Extracts all ARG_SELECTOR values for a given plural/selectordinal argument.
     *
     * @param Part $argStartPart The ARG_START part of the argument.
     * @return array<string> List of selector strings found.
     */
    private function extractSelectorsForArgument(Part $argStartPart): array
    {
        $selectors = [];
        $startIndex = null;
        $limitIndex = null;

        // Find the index of this ARG_START part and its corresponding ARG_LIMIT
        foreach ($this->pattern as $index => $part) {
            if ($part === $argStartPart) {
                $startIndex = $index;
                $limitIndex = $this->pattern->getLimitPartIndex($index);
                break;
            }
        }

        if ($startIndex === null || $limitIndex === null) {
            return $selectors;
        }

        // Iterate through parts between ARG_START and ARG_LIMIT
        foreach ($this->pattern as $index => $part) {
            if ($index > $startIndex && $index < $limitIndex && $part->getType() === TokenType::ARG_SELECTOR) {
                $selectors[] = $this->pattern->getSubstring($part);
            }
        }

        return $selectors;
    }

}