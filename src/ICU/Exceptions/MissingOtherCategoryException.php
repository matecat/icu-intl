<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 24/09/26
 *
 */

namespace Matecat\ICU\Exceptions;

use Throwable;

/**
 * Thrown when a plural/select/selectordinal style lacks the mandatory "other" category.
 * Extends BadPluralSelectPatternSyntaxException so existing catch blocks keep working.
 *
 * The message lists the selectors that were found, so a translated keyword (e.g. "autre")
 * is visible regardless of where it appears in the pattern.
 */
class MissingOtherCategoryException extends BadPluralSelectPatternSyntaxException
{
    /**
     * @param string $argumentType lowercase argument type: "plural", "select" or "selectordinal"
     * @param string|null $argumentName argument name/number, null for standalone styles
     * @param list<string> $selectors selectors found in the style, in pattern order
     */
    public function __construct(
        private readonly string $argumentType,
        private readonly ?string $argumentName,
        private readonly array $selectors,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $subject = $argumentType . ($argumentName !== null ? ' argument "' . $argumentName . '"' : ' style');
        $found = $selectors === [] ? 'none' : implode(', ', $selectors);

        InvalidArgumentException::__construct(
            'Category "other" missing from ICU message: ' . $subject . ' (found: ' . $found . ')',
            $code,
            $previous
        );
    }

    public function getArgumentType(): string
    {
        return $this->argumentType;
    }

    public function getArgumentName(): ?string
    {
        return $this->argumentName;
    }

    /**
     * @return list<string>
     */
    public function getSelectors(): array
    {
        return $this->selectors;
    }
}
