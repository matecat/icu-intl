<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 30/10/24
 * Time: 13:08
 *
 */


if (!@include_once 'lib/Bootstrap.php') {
    header("Location: configMissing");
}

//Bootstrap::start();
ini_set("intl.default_locale", "en_US");


try {
    $fmt = new MessageFormatter(
        "en_US",
        "{0,number,integer} monkeys on {1,number,integer} trees make {2,number} monkeys per tree\n"
    );

    echo "Pattern:\n";
    echo $fmt->getPattern() . "\n";

    echo "Empty pattern values provided:\n";
    echo $fmt->format([]) . "\n";

    echo "Non-empty pattern values provided:\n";
    echo MessageFormatter::formatMessage("en_US", $fmt->getPattern(), [200, 2, 100.1]) . "\n----\n";
} catch (IntlException $e) {
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
}


try {
    $fmt = new MessageFormatter(
        "en_US",
        "{monkeys,number,integer} monkeys on {trees,number,integer} trees make {distribution,number} monkeys per tree\n"
    );

    echo "Pattern:\n";
    echo $fmt->getPattern() . "\n";

    echo "Empty pattern values provided:\n";
    echo $fmt->format([]) . "\n";

    echo "Non-empty pattern values provided:\n";
    echo MessageFormatter::formatMessage(
            "en_US",
            $fmt->getPattern(),
            ["monkeys" => 200, "trees" => 2, "distribution" => 100.1]
        ) . "\n----\n";
} catch (IntlException $e) {
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
}


try {
    $fmt = new MessageFormatter(
        "en_US",
        "{organizer_gender, select, female{{organizer_name} has invited you to her party!} male{{organizer_name} has invited you to his party!} multiple{{organizer_name} have invited you to their party!} other{{organizer_name} has invited you to their party!}}\n"
    );

    echo "Pattern:\n";
    echo $fmt->getPattern() . "\n";

    echo "Empty pattern values provided:\n";
    echo $fmt->format([]) . "\n";

    echo "Non-empty pattern values provided:\n";
    echo MessageFormatter::formatMessage("en_US", $fmt->getPattern(), [
            "organizer_name" => "Ciccia",
            "organizer_gender" => "female"
        ]) . "\n----\n";
} catch (IntlException $e) {
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
}

try {
    $fmt = new MessageFormatter(
        "en_US",
        "{apples, plural, =0{There are no apples} =1{There is one apple...} other{There are some apples!}}\n"
    );

    echo "Pattern:\n";
    echo $fmt->getPattern() . "\n";

    echo "Empty pattern values provided:\n";
    echo $fmt->format([]) . "\n";

    echo "Non-empty pattern values provided:\n";
    echo MessageFormatter::formatMessage("en_US", $fmt->getPattern(), [
            "apples" => 41,
        ]) . "\n----\n";
} catch (IntlException $e) {
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
}


try {
    $fmt = new MessageFormatter(
        "en_US",
        "{apples, plural, =0{There are no apples} =1{There is one apple...} other{There are some apples!}}"
    );

    echo "Parse formatted:\n";
    $str = $fmt->parse("There is one apple...");

    if (!$str) {
        echo "ERROR: " . $fmt->getErrorMessage() . " (" . $fmt->getErrorCode() . ")" . "\n----\n";;
    } else {
        echo var_export($str, true) . "\n----\n";
    }
} catch (IntlException $e) {
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
}


try {
    echo "Parse formatted:\n";
    $str = MessageFormatter::parseMessage(
        "en",
        "{0,number,integer} monkeys on {1,number,integer} trees make {2,number} monkeys per tree",
        "4,560 monkeys on 123 trees make 37.073 monkeys per tree"
    );

    if (!$str) {
        echo "ERROR: " . $fmt->getErrorMessage() . " (" . $fmt->getErrorCode() . ")" . "\n----\n";;
    } else {
        echo var_export($str, true) . "\n----\n";
    }
} catch (IntlException $e) {
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
}

try {
    $fmt = new MessageFormatter("en_US", "{monkeyName} monkeys per tree\n");

    echo "Pattern:\n";
    echo $fmt->getPattern() . "\n";

    echo "Empty pattern values provided:\n";
    echo $fmt->format([]) . "\n";

    echo "Non-empty pattern values provided:\n";
    echo MessageFormatter::formatMessage("en_US", "{monkeyName} monkeys per {0}", ["ciccio", "monkeyName" => "Pippo"]
        ) . "\n----\n";
} catch (IntlException $e) {
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
}

try {
    $fmt = new MessageFormatter("en_US", "Oge ịgba ụgwọ gị nke ugbu a ga-agwụ na {days, plural, one{# ụbọchị} other{# ụbọchị}} na {hours, plural, one{# awa} other{awa #} }. {changesMadeByString}");

    echo "Pattern:\n";
    echo $fmt->getPattern() . "\n";

    echo "Empty pattern values provided:\n";
    echo $fmt->format(['days' => 1, "hours" => 23]) . "\n";

    echo "Non-empty pattern values provided:\n";
    echo MessageFormatter::formatMessage("en_US", "NO ICU", []) . "\n----\n";
} catch (IntlException $e) {
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
}


$fmt = new MessageFormatter("en_US", '{0, plural,=0{You have no messages} one{You have one message} other{You have a message \'\'}}');
echo "Pattern:\n";
echo $fmt->getPattern() . "\n";

echo "Empty pattern values provided:\n";
echo $fmt->format([23]) . "\n";

echo "Non-empty pattern values provided:\n";
echo MessageFormatter::formatMessage("en_US", "NO ICU", []) . "\n----\n";
