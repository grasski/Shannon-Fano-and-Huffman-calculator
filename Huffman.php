<!DOCTYPE html>
<html>
<head>
    <link type="text/css" rel="stylesheet" href="css.css">
</head>
<body>
</body>
</html>

<?php
class HuffmanCode{
    static array $code;
    function createCode($probability){
        self::$code = [];

        foreach ($probability as $key=> $item){
            self::$code[$key] = null;
        }

        while (count($probability) > 1){
            $keys = array_keys($probability);
            $lastKey = $keys[count($keys)-1];
            $penultimateKey = $keys[count($keys)-2];
            $last = $probability[$lastKey];
            $penultimate = $probability[$penultimateKey];
            unset($probability[$lastKey]);
            unset($probability[$penultimateKey]);
            $new = null;
            $new[$lastKey.$penultimateKey] = $last + $penultimate;

            $array = str_split($lastKey);
            foreach ($array as $value){
                self::$code[$value] = "0" . self::$code[$value];
            }
            $array = str_split($penultimateKey);
            foreach ($array as $value){
                self::$code[$value] = "1" . self::$code[$value];
            }

            $probability[key($new)] = $last + $penultimate;
            arsort($probability);
        }
    }

    function run($text){
        $sp = new SupportFunctions();

        $frequency = $sp->analyzeTextToFrequency($text);
        $probability = $sp->probability($frequency, strlen($text));
        $this->createCode($probability);
        $avgL = $sp->averageLenght($frequency, self::$code);
        $H = $sp->entropy($probability);

        echo "<div class='column'>";
        echo "<h1>Huffmanova metoda.</h1>";
        echo "<p>Zadaný text: <B>" . $text. "</B><BR>";
        echo "Délka textu: <B>" . strlen($text) . "</B><BR>";
        echo "Průměrná délka kódového slova: <B>" . $avgL . "</B><BR>";
        echo "Entropie: <B>" . $H . "</B><BR>";
        echo "Efektivita: <B>" . $sp->efficiency($avgL,$H) . "%</B><BR>";
        echo "Zakódovaný řetězec: <B>". $sp->binaryString($text, self::$code) . "</B></p><BR>";

        $sp->createTable(["Hodnota", "Četnost", "Pravděpodobnost", "Kód"],$frequency, $probability, self::$code);
        echo "</div>";
    }
}