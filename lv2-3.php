<?php
function handleFoundElement($resource, $element, $attributes) {
    if ($element == 'RECORD') {
        echo '<div style="border: 2px solid #000000; padding: 5px">';
    } elseif ($element == 'SLIKA') {
        echo '<h2>' . $element . ': </h2>';
        echo '<div>
                    <img src="' . $element . '"/>
              </div>';
    } elseif ($element == 'IME' || $element == 'PREZIME' || $element == 'EMAIL' || $element == 'ZIVOTOPIS' || $element == 'SPOL') {
        echo '<h2>' . $element . ': </h2>';
    }
}

function handleEndElement($resource, $element) {
    if ($element == 'RECORD') {
        echo '</div>';
    } elseif ($element == 'IME' || $element == 'PREZIME' || $element == 'EMAIL' || $element == 'ZIVOTOPIS') {
        echo '<br>';
    }
}

function handleCharacterData($resource, $characterData) {
    echo $characterData;
}

$resource = xml_parser_create();

xml_set_element_handler($resource, 'handleFoundElement', 'handleEndElement');

xml_set_character_data_handler($resource, 'handleCharacterData');

$file = 'LV2.xml';
$fp = @fopen($file, 'r') or die("<p>Can't open file' $file'.</p></body></html>");

while ($data = fread($fp, 4096)) {
    xml_parse($resource, $data, feof($fp));
}
xml_parser_free($resource);
