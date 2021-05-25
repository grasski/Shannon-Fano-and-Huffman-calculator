<?php

class SupportFunctions
{
    //Analýza textu a vytvoření četnosti
    function analyzeTextToFrequency($text): array{
        $arr = str_split($text,1);
        $frequency = (array_count_values($arr));
        arsort($frequency);  //Seřazení pole od největšího prvku

        return $frequency;
    }

    //Výpočet pravděpodobnosti
    function probability($fr, $textLen): array{
        $probability = [];
        foreach ($fr as $key=>$value){
            $probability[$key] = $value/$textLen;
        }
        return $probability;
    }

    //Výpočet průměrné délky slova
    function averageLenght($fr, $code): float
    {
        $sum = 0;
        $countSymbols = 0;
        foreach ($fr as $key=> $value){
            $sum += $value * strlen($code[$key]);
            $countSymbols += $value;
        }
        return round($sum/$countSymbols, 3);
    }

    //Výpočet entropie
    function entropy($probability): float
    {
        $H = 0;
        foreach ($probability as $value){
            $H += -$value*log($value, 2);
        }
        return round($H, 3);
    }

    //Vypočet efektivnosti
    function efficiency($avgL, $H): float
    {
        return round(($H/$avgL)*100, 3);
    }

    function binaryString($text, $code){
        $char = str_split($text,1);
        $str = "";
        foreach ($char as $value){
            $str .= $code[$value] . " ";
        }
        return $str;
    }

    //Automatické vytvoření tabulky
    function createTable($column, $fr, $probability, $code){
        echo "<table><tr>";
        foreach ($column as $item){
            echo "<th>". $item ."</th>";
        }
        echo "</tr>";
        foreach ($fr as $key=> $value){
            echo "<tr>";
            echo "<td>".$key."</td>";
            echo "<td>".$value."</td>";
            echo "<td>".$probability[$key]."</td>";
            echo "<td>".$code[$key]."</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}