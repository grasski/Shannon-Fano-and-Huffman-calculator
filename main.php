<!DOCTYPE html>
<html>
<head>
    <link type="text/css" rel="stylesheet" href="css.css">
</head>
<body>
</body>
</html>
<?php
include "SupportFunctions.php";
require "input.php";
class main
{
    function getText(): string{
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            $text = $_POST["text"];
            $sp = new SupportFunctions();
            if (count($sp->analyzeTextToFrequency($text)) > 1){
                return $text;
            } else{
                echo "<BR>Zadejte text ve formuláři.";
                header("Location:input.php");
            }
        } else{
            echo "<BR>Zadejte text ve formuláři.";
            header("Location:input.php");
        }
    }
}
$main = new main();
$text = $main->getText();
if ($text != null){
    include "Shannon-Fano.php";
    include "Huffman.php";

    $shanno = new ShannonCode();
    $huff = new HuffmanCode();

    echo "<div class='row'>";
    $shanno->run($text);
    $huff->run($text);
    echo "</div>";
}