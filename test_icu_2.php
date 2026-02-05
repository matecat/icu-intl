<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 22/01/26
 * Time: 13:25
 *
 */

include 'vendor/autoload.php';

use Matecat\ICU\MessagePattern;
use Matecat\ICU\Parts\TokenType;


$fmt = new MessageFormatter(
    'en_US',
    "{organizer_gender, select, female{{organizer_name} has invited you to her party!} male{{organizer_name} has invited you to his party!} multiple{{organizer_name} have invited you to their party!} other{{organizer_name} has invited you to their party!}}"
);
echo var_export($fmt->getPattern(), true) . "\n";

echo $fmt->format(["organizer_name" => "Ciccia", "organizer_gender" => "female"]) . "\n";

$res = $fmt->parse("Ciccia has invited you to her party!");
if ($res) {
    var_export($res);
} else {
    echo "ERROR: " . $fmt->getErrorMessage() . " (" . $fmt->getErrorCode() . ")\n";
}

//
//$parse = new MessagePattern($fmt->getPattern());
//foreach ($parse as $part) {
//    if ($part->getType() == TokenType::ARG_SELECTOR) {
//        echo $parse->getSubstring($part) . "\n   ";
//    }
//    if ($part->getType() == TokenType::ARG_NAME) {
//        echo $parse->getSubstring($part) . "\n";
//    }
//}
//usleep(1);


$message = <<<TEXT
{place, selectordinal,
  one {You got the gold medal}
  two {You got the silver medal}
  few {You got the bronze medal}
  other {You got in the {place}th place}
}
TEXT;

$message2 = "Hel'{o!";


$p = new MessageFormatter("en_US", $message2);

echo $p->format(["num" => 236]) . "\n";
//echo var_export($p->getPattern(), true) . "\n";

$parse = new MessagePattern($p->getPattern());
foreach ($parse as $part) {
    if ($part->getType() == TokenType::ARG_SELECTOR) {
        echo $parse->getSubstring($part) . "\n";
    }
    if ($part->getType() == TokenType::ARG_NAME) {
        echo "   " . $parse->getSubstring($part) . "\n";
    }
}

//foreach ($parse as $parts) {
//    printf(
//        "[%s] '%s' '%s' at %d..%d %d\n",
//        $parts->getType()->name,
//        ($parts->getType() == TokenType::ARG_START || $parts->getType() == TokenType::ARG_LIMIT) ? $parts->getArgType()->name : $parse->getSubstring($parts),
//        $parts->getValue(),
//        $parts->getIndex(),
//        $parts->getLimit(),
//        $parts->getLength()
//    );
//}

$indent = '';
foreach ($parse as $i => $part) {
    $explanation = '';

    $partString = (string)$part;
    $type = $part->getType();

    if ($type === TokenType::MSG_START) {
        $indent = str_pad('', $part->getValue() * 4, ' ', STR_PAD_LEFT);
    }

    if ($part->getLength() > 0) {
        $explanation .= '="' . $parse->getSubstring($part) . '"';
    }

    if ($type->hasNumericValue()) {
        $explanation .= '=' . $parse->getNumericValue($part);
    }

    printf("%2d: %s%s%s\n", $i, $indent, $partString, $explanation);

    if ($type === TokenType::MSG_LIMIT) {
        $nestingLevel = $part->getValue();
        if ($nestingLevel > 1) {
            $indent = str_pad('', ($nestingLevel - 1) * 4, ' ', STR_PAD_LEFT);
        } else {
            $indent = '';
        }
    }

}