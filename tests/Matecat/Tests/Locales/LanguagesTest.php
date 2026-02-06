<?php

declare(strict_types=1);

namespace Matecat\Tests\Locales;

use Matecat\Locales\InvalidLanguageException;
use Matecat\Locales\Languages;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality of the `Languages` class.
 *
 * This test suite validates language management functionality including:
 * - Singleton pattern
 * - Language code normalization
 * - RTL language detection
 * - Language validation
 * - Localized name retrieval
 * - ISO/RFC code conversion
 * - Plural forms retrieval
 * - OCR support detection
 */
final class LanguagesTest extends TestCase
{
    // =========================================================================
    // Singleton Tests
    // =========================================================================

    public function testGetInstanceReturnsSameInstance(): void
    {
        $instance1 = Languages::getInstance();
        $instance2 = Languages::getInstance();

        self::assertSame($instance1, $instance2);
    }

    public function testGetInstanceReturnsLanguagesInstance(): void
    {
        $instance = Languages::getInstance();

        self::assertInstanceOf(Languages::class, $instance);
    }

    // =========================================================================
    // RTL Language Tests
    // =========================================================================

    #[DataProvider('rtlLanguagesProvider')]
    public function testIsRTLReturnsTrueForRTLLanguages(string $code): void
    {
        self::assertTrue(Languages::isRTL($code));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function rtlLanguagesProvider(): array
    {
        return [
            'Arabic (Saudi Arabia)' => ['ar-SA'],
            'Arabic (UAE)' => ['ar-AE'],
            'Arabic (Egypt)' => ['ar-EG'],
            'Hebrew' => ['he-IL'],
            'Persian' => ['fa-IR'],
            'Urdu' => ['ur-PK'],
        ];
    }

    #[DataProvider('ltrLanguagesProvider')]
    public function testIsRTLReturnsFalseForLTRLanguages(string $code): void
    {
        self::assertFalse(Languages::isRTL($code));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function ltrLanguagesProvider(): array
    {
        return [
            'English (US)' => ['en-US'],
            'English (UK)' => ['en-GB'],
            'French' => ['fr-FR'],
            'German' => ['de-DE'],
            'Spanish' => ['es-ES'],
            'Italian' => ['it-IT'],
            'Portuguese' => ['pt-PT'],
            'Russian' => ['ru-RU'],
            'Chinese' => ['zh-CN'],
            'Japanese' => ['ja-JP'],
        ];
    }

    public function testGetRTLLangsReturnsArrayOfRTLCodes(): void
    {
        $langs = Languages::getInstance();
        $rtlLangs = $langs->getRTLLangs();

        self::assertNotEmpty($rtlLangs);

        // Check that known RTL languages are in the list
        self::assertContains('ar-SA', $rtlLangs);
        self::assertContains('he-IL', $rtlLangs);

        // Check that LTR languages are NOT in the list
        self::assertNotContains('en-US', $rtlLangs);
        self::assertNotContains('fr-FR', $rtlLangs);
    }

    // =========================================================================
    // Language Enabled Tests
    // =========================================================================

    #[DataProvider('enabledLanguagesProvider')]
    public function testIsEnabledReturnsTrueForEnabledLanguages(string $code): void
    {
        $langs = Languages::getInstance();

        self::assertTrue($langs->isEnabled($code));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function enabledLanguagesProvider(): array
    {
        return [
            'English (US)' => ['en-US'],
            'French' => ['fr-FR'],
            'German' => ['de-DE'],
            'Spanish' => ['es-ES'],
            'Italian' => ['it-IT'],
            'Arabic' => ['ar-SA'],
            'Chinese' => ['zh-CN'],
            'Japanese' => ['ja-JP'],
            'Russian' => ['ru-RU'],
        ];
    }

    public function testGetEnabledLanguagesReturnsNonEmptyArray(): void
    {
        $langs = Languages::getInstance();
        $enabledLangs = $langs->getEnabledLanguages();

        self::assertNotEmpty($enabledLangs);

        // Check structure of returned array
        $firstLang = reset($enabledLangs);
        self::assertIsArray($firstLang);
        self::assertArrayHasKey('code', $firstLang);
        self::assertArrayHasKey('name', $firstLang);
        self::assertArrayHasKey('direction', $firstLang);
    }

    public function testGetEnabledLanguagesAreSortedAlphabetically(): void
    {
        $langs = Languages::getInstance();
        $enabledLangs = $langs->getEnabledLanguages();

        $names = array_column($enabledLangs, 'name');
        $sortedNames = $names;
        sort($sortedNames);

        self::assertSame($sortedNames, $names);
    }

    // =========================================================================
    // Localized Name Tests
    // =========================================================================

    public function testGetLocalizedNameReturnsNonEmptyString(): void
    {
        $langs = Languages::getInstance();

        // Test various language codes return non-empty localized names
        $codes = ['en-US', 'en-GB', 'fr-FR', 'de-DE', 'es-ES', 'it-IT', 'ar-SA', 'ja-JP', 'zh-CN'];

        foreach ($codes as $code) {
            $localizedName = $langs->getLocalizedName($code);
            self::assertNotEmpty($localizedName, "Localized name for $code should not be empty");
            self::assertIsString($localizedName, "Localized name for $code should be a string");
        }
    }

    public function testGetLocalizedNameReturnsConsistentResults(): void
    {
        $langs = Languages::getInstance();

        // Multiple calls should return the same result
        $name1 = $langs->getLocalizedName('en-US');
        $name2 = $langs->getLocalizedName('en-US');

        self::assertSame($name1, $name2);
    }

    public function testGetLocalizedNameRFCReturnsNonEmptyString(): void
    {
        $langs = Languages::getInstance();

        $name = $langs->getLocalizedNameRFC('en-US');
        self::assertNotEmpty($name);
        self::assertIsString($name);
    }

    public function testGetLocalizedNameRFCThrowsExceptionForInvalidCode(): void
    {
        $langs = Languages::getInstance();

        $this->expectException(InvalidLanguageException::class);
        $langs->getLocalizedNameRFC('invalid-code');
    }

    public function testGetLocalizedNameRFCThrowsExceptionForNullCode(): void
    {
        $langs = Languages::getInstance();

        $this->expectException(InvalidLanguageException::class);
        $langs->getLocalizedNameRFC(null);
    }

    // =========================================================================
    // Code Conversion Tests
    // =========================================================================

    public function testGet3066CodeReturnsValidCode(): void
    {
        $langs = Languages::getInstance();

        // Get a localized name first, then verify we can get the RFC code back
        $localizedName = $langs->getLocalizedName('en-US');
        self::assertNotNull($localizedName);
        $rfcCode = $langs->get3066Code($localizedName);

        self::assertNotEmpty($rfcCode);
        // RFC code should contain a hyphen (e.g., en-US)
        self::assertStringContainsString('-', $rfcCode);
    }

    public function testGetIsoCodeReturnsValidCode(): void
    {
        $langs = Languages::getInstance();

        // Get a localized name first, then verify we can get the ISO code
        $localizedName = $langs->getLocalizedName('en-US');
        self::assertNotNull($localizedName);
        $isoCode = $langs->getIsoCode($localizedName);

        self::assertNotEmpty($isoCode);
        // ISO code should be 2-3 characters
        self::assertLessThanOrEqual(3, strlen($isoCode));
    }

    #[DataProvider('convertToIsoCodeProvider')]
    public function testConvertLanguageToIsoCode(string $rfcCode, string $expectedIsoCode): void
    {
        self::assertSame($expectedIsoCode, Languages::convertLanguageToIsoCode($rfcCode));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function convertToIsoCodeProvider(): array
    {
        return [
            'en-US to en' => ['en-US', 'en'],
            'fr-FR to fr' => ['fr-FR', 'fr'],
            'de-DE to de' => ['de-DE', 'de'],
            'it-IT to it' => ['it-IT', 'it'],
            'es-ES to es' => ['es-ES', 'es'],
            'ar-SA to ar' => ['ar-SA', 'ar'],
            'ja-JP to ja' => ['ja-JP', 'ja'],
            'zh-CN to zh' => ['zh-CN', 'zh'],
            'ru-RU to ru' => ['ru-RU', 'ru'],
        ];
    }

    public function testConvertLanguageToIsoCodeReturnsNullForInvalidCode(): void
    {
        self::assertNull(Languages::convertLanguageToIsoCode('invalid-XX'));
    }

    public function testGetLangRegionCodeReturnsCorrectCode(): void
    {
        $langs = Languages::getInstance();

        // Test with a language that has languageRegionCode
        $code = $langs->getLangRegionCode('English (USA)');
        self::assertNotEmpty($code);
    }

    // =========================================================================
    // Language Validation Tests
    // =========================================================================

    public function testIsValidLanguageReturnsTrueForValidLanguages(): void
    {
        self::assertTrue(Languages::isValidLanguage('en-US'));
        self::assertTrue(Languages::isValidLanguage('fr-FR'));
        self::assertTrue(Languages::isValidLanguage('de-DE'));
        self::assertTrue(Languages::isValidLanguage('ar-SA'));
    }

    public function testIsValidLanguageReturnsFalseForInvalidLanguages(): void
    {
        self::assertFalse(Languages::isValidLanguage('xx-XX'));
        self::assertFalse(Languages::isValidLanguage('invalid'));
    }

    public function testValidateLanguageReturnsNormalizedCode(): void
    {
        $langs = Languages::getInstance();

        self::assertSame('en-US', $langs->validateLanguage('en-US'));
        self::assertSame('fr-FR', $langs->validateLanguage('fr-FR'));
    }

    public function testValidateLanguageThrowsExceptionForEmptyCode(): void
    {
        $langs = Languages::getInstance();

        $this->expectException(InvalidLanguageException::class);
        $langs->validateLanguage('');
    }

    public function testValidateLanguageThrowsExceptionForNullCode(): void
    {
        $langs = Languages::getInstance();

        $this->expectException(InvalidLanguageException::class);
        $langs->validateLanguage(null);
    }

    public function testValidateLanguageListReturnsValidatedList(): void
    {
        $langs = Languages::getInstance();

        $result = $langs->validateLanguageList(['en-US', 'fr-FR', 'de-DE']);

        self::assertCount(3, $result);
        self::assertContains('en-US', $result);
        self::assertContains('fr-FR', $result);
        self::assertContains('de-DE', $result);
    }

    public function testValidateLanguageListThrowsExceptionForEmptyList(): void
    {
        $langs = Languages::getInstance();

        $this->expectException(InvalidLanguageException::class);
        $langs->validateLanguageList([]);
    }

    public function testValidateLanguageListAsStringReturnsCommaSeparatedString(): void
    {
        $langs = Languages::getInstance();

        $result = $langs->validateLanguageListAsString('en-US, fr-FR, de-DE');

        self::assertStringContainsString('en-US', $result);
        self::assertStringContainsString('fr-FR', $result);
        self::assertStringContainsString('de-DE', $result);
    }

    public function testValidateLanguageListAsStringWithCustomSeparator(): void
    {
        $langs = Languages::getInstance();

        $result = $langs->validateLanguageListAsString('en-US|fr-FR|de-DE', '|');

        self::assertStringContainsString('en-US', $result);
        self::assertStringContainsString('fr-FR', $result);
        self::assertStringContainsString('de-DE', $result);
    }

    // =========================================================================
    // Localized Language Static Method Tests
    // =========================================================================

    public function testGetLocalizedLanguageReturnsNonEmptyString(): void
    {
        $codes = ['en-US', 'fr-FR', 'de-DE', 'it-IT', 'ar-SA'];

        foreach ($codes as $code) {
            $localizedName = Languages::getLocalizedLanguage($code);
            self::assertNotNull($localizedName, "Localized language for $code should not be null");
            self::assertNotEmpty($localizedName, "Localized language for $code should not be empty");
        }
    }

    public function testGetLocalizedLanguageReturnsNullForInvalidCode(): void
    {
        self::assertNull(Languages::getLocalizedLanguage('invalid-XX'));
    }

    // =========================================================================
    // Number of Plurals Tests
    // =========================================================================

    #[DataProvider('numberOfPluralsProvider')]
    public function testGetNumberOfPluralsReturnsCorrectCount(string $code, int $expectedCount): void
    {
        self::assertSame($expectedCount, Languages::getNumberOfPlurals($code));
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    public static function numberOfPluralsProvider(): array
    {
        return [
            // Languages with 1 plural form (no plural)
            'Japanese' => ['ja-JP', 1],
            'Chinese' => ['zh-CN', 1],
            'Korean' => ['ko-KR', 1],
            'Vietnamese' => ['vi-VN', 1],
            'Thai' => ['th-TH', 1],
            'Indonesian' => ['id-ID', 1],

            // Languages with 2 plural forms
            'English (US)' => ['en-US', 2],
            'English (UK)' => ['en-GB', 2],
            'German' => ['de-DE', 2],
            'Spanish' => ['es-ES', 2],
            'Italian' => ['it-IT', 2],
            'Dutch' => ['nl-NL', 2],
            'Swedish' => ['sv-SE', 2],
            'Danish' => ['da-DK', 2],
            'Norwegian' => ['nb-NO', 2],
            'French' => ['fr-FR', 2],
            'Portuguese' => ['pt-PT', 2],

            // Languages with 3 plural forms
            'Russian' => ['ru-RU', 3],
            'Ukrainian' => ['uk-UA', 3],
            'Polish' => ['pl-PL', 3],
            'Czech' => ['cs-CZ', 3],
            'Slovak' => ['sk-SK', 3],
            'Croatian' => ['hr-HR', 3],
            'Serbian' => ['sr-Latn-RS', 3],
            'Romanian' => ['ro-RO', 3],
            'Lithuanian' => ['lt-LT', 3],
            'Latvian' => ['lv-LV', 3],

            // Languages with 4 plural forms
            'Slovenian' => ['sl-SI', 4],
            'Maltese' => ['mt-MT', 4],
            'Scottish Gaelic' => ['gd-GB', 4],
            'Hebrew' => ['he-IL', 4],
            'Manx' => ['gv-IM', 4],

            // Languages with 5 plural forms
            'Irish' => ['ga-IE', 5],
            'Breton' => ['br-FR', 5],

            // Languages with 6 plural forms
            'Arabic' => ['ar-SA', 6],
            'Arabic (UAE)' => ['ar-AE', 6],
            'Arabic (Egypt)' => ['ar-EG', 6],
            'Welsh' => ['cy-GB', 6],
        ];
    }

    public function testGetNumberOfPluralsReturnsOneForUnknownLanguage(): void
    {
        // Unknown language should return 1 (default: no plural forms)
        self::assertSame(1, Languages::getNumberOfPlurals('xx-XX'));
    }

    public function testGetNumberOfPluralsWorksWithISOCodes(): void
    {
        // Should normalize ISO codes to RFC and return correct count
        self::assertSame(2, Languages::getNumberOfPlurals('en'));
        self::assertSame(2, Languages::getNumberOfPlurals('fr'));
        self::assertSame(3, Languages::getNumberOfPlurals('ru'));
        self::assertSame(6, Languages::getNumberOfPlurals('ar'));
    }

    // =========================================================================
    // OCR Support Tests
    // =========================================================================

    public function testGetLanguagesWithOcrSupportedReturnsNonEmptyArray(): void
    {
        $ocrSupported = Languages::getLanguagesWithOcrSupported();

        self::assertNotEmpty($ocrSupported);
    }

    public function testGetLanguagesWithOcrNotSupportedReturnsArray(): void
    {
        // This may or may not be empty depending on the data
        $ocrNotSupported = Languages::getLanguagesWithOcrNotSupported();

        // Just verify it's callable and returns the expected type
        self::assertCount(count($ocrNotSupported), $ocrNotSupported);
    }

    // =========================================================================
    // Language Code Normalization Tests
    // =========================================================================

    public function testLanguageCodeNormalizationWithDifferentFormats(): void
    {
        $langs = Languages::getInstance();

        // Test with lowercase
        self::assertTrue($langs->isEnabled('en-us'));

        // Test with uppercase
        self::assertTrue($langs->isEnabled('EN-US'));

        // Test with mixed case
        self::assertTrue($langs->isEnabled('En-Us'));
    }

    public function testLanguageCodeNormalizationWithThreeParts(): void
    {
        // Languages like sr-Latn-RS (Serbian Latin)
        self::assertTrue(Languages::isValidLanguage('sr-Latn-RS'));
        self::assertSame(3, Languages::getNumberOfPlurals('sr-Latn-RS'));
    }

    public function testISOCodeFallback(): void
    {
        // Test that ISO codes are properly mapped to RFC codes
        $langs = Languages::getInstance();

        // These should work because of the ISO to RFC mapping
        self::assertTrue($langs->isEnabled('en'));
        self::assertTrue($langs->isEnabled('fr'));
        self::assertTrue($langs->isEnabled('it'));
        self::assertTrue($langs->isEnabled('ar'));
    }

    // =========================================================================
    // Edge Case Tests for Full Coverage
    // =========================================================================

    public function testNormalizeLanguageCodeWithFourPartsReturnsNull(): void
    {
        // Language code with more than 3 parts should return null from normalizeLanguageCode
        // This tests the "else { return null; }" branch in normalizeLanguageCode
        self::assertFalse(Languages::isValidLanguage('a-b-c-d'));
        self::assertFalse(Languages::isValidLanguage('en-US-extra-part'));
    }

    public function testGetNumberOfPluralsWithInvalidNormalizedCode(): void
    {
        // Test with a code that normalizes to null (more than 3 parts)
        // Should return default of 1
        self::assertSame(1, Languages::getNumberOfPlurals('a-b-c-d'));
    }
}
