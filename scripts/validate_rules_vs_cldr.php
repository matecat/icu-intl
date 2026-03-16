#!/usr/bin/env php
<?php
/**
 * Validates PluralRules.php against the CLDR 49 reference (cldr49_plural_rules.json).
 *
 * Checks:
 * 1. Cardinal category count matches CLDR
 * 2. Cardinal category names match CLDR
 * 3. Ordinal category count matches CLDR
 * 4. Ordinal category names match CLDR
 *
 * Usage: php scripts/validate_rules_vs_cldr.php
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Matecat\ICU\Plurals\PluralRules;

$cldrPath = __DIR__ . '/cldr_sources/cldr49_plural_rules.json';
$cldrJson = file_get_contents($cldrPath);
if ($cldrJson === false) {
    fwrite(STDERR, "Failed to read $cldrPath\n");
    exit(1);
}
$cldr = json_decode($cldrJson, true, 512, JSON_THROW_ON_ERROR);

// Get all languages from rulesMap via reflection
$ref = new ReflectionClass(PluralRules::class);
$prop = $ref->getProperty('rulesMap');
/** @var array<string, array{cardinal: int, ordinal: int}> $rulesMap */
$rulesMap = $prop->getValue();

$issues = [];
$checked = 0;
$skipped = 0;

foreach ($rulesMap as $isoCode => $ruleGroups) {
    // Skip languages not in CLDR (they're custom/alternate codes)
    if (!isset($cldr[$isoCode])) {
        $skipped++;
        continue;
    }

    $checked++;
    $cldrLang = $cldr[$isoCode];

    // --- Check cardinal categories ---
    $ourCardinalCats = PluralRules::getCardinalCategories($isoCode);
    $cldrCardinalCats = array_column($cldrLang['cardinal'], 'category');

    if ($ourCardinalCats !== $cldrCardinalCats) {
        $issues[] = sprintf(
            "CARDINAL MISMATCH %s (%s): ours=[%s] cldr=[%s] (rule group %d)",
            $isoCode,
            $cldrLang['name'],
            implode(',', $ourCardinalCats),
            implode(',', $cldrCardinalCats),
            $ruleGroups['cardinal']
        );
    }

    // --- Check ordinal categories ---
    $ourOrdinalCats = PluralRules::getOrdinalCategories($isoCode);
    $cldrOrdinalCats = array_column($cldrLang['ordinal'], 'category');

    if ($ourOrdinalCats !== $cldrOrdinalCats) {
        // Special case: if CLDR has no ordinal data, and we default to ['other'], that's acceptable
        if (empty($cldrOrdinalCats) && $ourOrdinalCats === ['other']) {
            continue;
        }
        $issues[] = sprintf(
            "ORDINAL MISMATCH  %s (%s): ours=[%s] cldr=[%s] (rule group %d)",
            $isoCode,
            $cldrLang['name'],
            implode(',', $ourOrdinalCats),
            implode(',', $cldrOrdinalCats),
            $ruleGroups['ordinal']
        );
    }
}

echo "Checked $checked languages against CLDR 49 ($skipped skipped - not in CLDR)\n\n";

if (empty($issues)) {
    echo "✅ All languages match CLDR 49!\n";
} else {
    echo "❌ Found " . count($issues) . " mismatches:\n\n";
    foreach ($issues as $issue) {
        echo "  $issue\n";
    }
}

exit(empty($issues) ? 0 : 1);

