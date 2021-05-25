<!DOCTYPE html>
<html>
<head>
    <link type="text/css" rel="stylesheet" href="css.css">
</head>
<body>
</body>
</html>

<?php
class ShannonCode{
    //Rozděluje input na dvě pole s podobnou četností
    function slice($array){
        if (count($array) > 1) {
            $intKeys = array_keys($array);
            $sum = array_sum($array);   // celková četnost pole
            $sliceIndex = 0;
            //Spočítání četností jednotlivých stran a získání indexu pro rozdělení pole
            $leftSum = 0;
            $rightSum = 0;
            foreach ($array as $key) {
                $leftSum += $key;
                $sliceIndex++;
                if ($leftSum >= ($sum / 2)) {
                    //Spočítání pravé strany pole
                    for ($i = $sliceIndex; $i <= (count($array) - 1); $i++) {
                        $rightSum += $array[$intKeys[$i]];
                    }

                    //Pokud je levá strana o větší nebo rovna než pravá, můžeme z ní odečíst její poslední prvek a ten přičíst k pravé straně - rozdíl je pak menší
                    if ((($leftSum - $array[$intKeys[$sliceIndex-1]]) >= $rightSum) and $sliceIndex > 1) {
                        $leftSum -= $array[$intKeys[$sliceIndex-1]];
                        $rightSum += $array[$intKeys[$sliceIndex-1]];
                        $sliceIndex -= 1;
                    }
                    break;
                }
            }
            $secondSlice = (array_slice($array, $sliceIndex,null, true));
            $firstSlice = (array_slice($array, 0, $sliceIndex, true));
            return [$firstSlice, $secondSlice];
        }
    }

    static array $code;
    static array $nulls;
    static array $ones;
    static int $i;
    //Hlavní funkce pro sestavení stromu z nul a jedniček rozdělených pomocí pravidel Fannon-Fannova kódování
    //Vždy se prochází první (nultá) část pole a následně se prvky rozdělují a přidávají do dalších polí
    //pokud je nultá řada hotová (zbyde pouze jeden prvek), přechází se na další větev a tak dokola.
    function buildTree($first, $second){
        if (!is_array($first) && !is_array($second)){
            return null;
        }
        $branch = $first;

        foreach ($first as $key=>$item){
            self::$code[$key] = "0";
        }
        foreach ($second as $key=>$item){
            self::$code[$key] = "1";
        }
        if (count($first) <=1 && count($second) <= 1){
            return;
        }
        self::$nulls[0] = $first;
        self::$ones[0] = $second;
        for ($j = 0; $j < 2; $j++){
            $temp1 = $branch;
            self::$i = 1;
            //Podud je první část malá, tak už se přepínám na druhou část
            if (count($temp1) <= 1){
                $branch = $second;
                $temp1 = $branch;
                $j++;
            }

            $part = $this->slice($temp1);
            $this->addNullOrOne($part);
            self::$nulls[self::$i] = $part[0];
            self::$ones[self::$i] = $part[1];
            while (count($part[0]) > 1){
                self::$i++;
                $part = $this->slice($part[0]);
                $this->addNullOrOne($part);

                self::$nulls[self::$i] = $part[0];
                self::$ones[self::$i] = $part[1];

                //Pokud se předává druhá část pole (z nul do jedniček) o velikosti větší než 1 do pole s jedničkama! - je potřeba toto pole znovu projet a rozdělit 0 a 1
                $part = $this->selection($part, 1,0);
            }
            $part = $this->slice($temp1);
            while (count($part[1]) > 1){
                self::$i++;
                $part = $this->slice($part[1]);
                $this->addNullOrOne($part);
                self::$nulls[self::$i] = $part[0];
                self::$ones[self::$i] = $part[1];

                //Pokud se předává první část pole (z jedniček do nul) o velikosti větší než 1 do pole s nulama! - je potřeba toto pole znovu projet a rozdělit 0 a 1
                $part = $this->selection($part,0, 1);
            }
            $branch = $second;
        }
    }

    //Podpůrná funkce pro zajištění funkčnosti a výběru správných hodnot při rozdělování polí
    function selection($part, $index1, $index2){
        if (count($part[$index1]) > 1){
            $temp2 = $part;
            while (count($part[$index1]) > 1){
                self::$i++;
                $part = $this->slice($part[$index1]);
                $this->addNullOrOne($part);
                self::$nulls[self::$i] = $part[0];
                self::$ones[self::$i] = $part[1];

                if (count($part[$index2]) > 1){
                    $temp3 = $part;
                    while (count($part[$index2]) > 1){
                        self::$i++;
                        $part = $this->slice($part[$index2]);
                        $this->addNullOrOne($part);
                        self::$nulls[self::$i] = $part[0];
                        self::$ones[self::$i] = $part[1];
                    }
                    $part = $temp3;
                }
            }
            $part = $temp2;
        }
        return $part;
    }

    //Přidání nul a jedniček do matice
    function addNullOrOne($array){
        foreach ($array[0] as $key=>$item){
            self::$code[$key] .= "0";
        }
        foreach ($array[1] as $key=>$item){
            self::$code[$key] .= "1";
        }
    }

    //Spouštění programu
    function run($text){
        $sp = new SupportFunctions();

        $frequency = $sp->analyzeTextToFrequency($text);
        $tree = $this->slice($frequency);
        if (is_array($tree)){
            $this->buildTree($tree[0], $tree[1]);
        }

        $probability = $sp->probability($frequency, strlen($text));
        $avgL = $sp->averageLenght($frequency, self::$code);
        $H = $sp->entropy($probability);

        echo "<div class='column'>";
        echo "<h1>Shannon-Fanova metoda.</h1>";
        echo "<p>Zadaný text: <B>" . $text. "</B><BR>";
        echo "Délka textu: <B>" . strlen($text) . "</B><BR>";
        echo "Průměrná délka kódového slova: <B>" . $avgL . "</B><BR>";
        echo "Entropie: <B>" . $H . "</B><BR>";
        echo "Efektivita: <B>" . $sp->efficiency($avgL,$H) . "%</B><BR>";
        echo "Zakódovaný řetězec: <B>". $sp->binaryString($text, self::$code) . "</B></p><BR>";

        $sp->createTable(["Hodnota", "Četnost", "Pravděpodobnost", "Kód"], $frequency, $probability, self::$code);
        echo "</div>";
    }
}