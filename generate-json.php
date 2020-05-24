<?php

class country {}

$row = 1;
$countryCodes = array();
if (($handle = fopen("./rawdata/countries.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
        $countryCodes[] = array($row,$data[0],$data[1]);
    }
    fclose($handle);
};

$countryR0list = array();
if (($handle = fopen("./rawdata/r0.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
        $found = FALSE;
        foreach ($countryCodes as $countrycode) {
            if ($countrycode[2]==$data[1]) {
                $country = new country;
                $country->id = $countrycode[1];
                $country->nr = $data[0];
                $country->value = $data[2];
                $countryR0list[] = $country;
                $found = TRUE;
            }
        }
        if (!$found) {
         //   echo "<br/>Not found: " . $data[1];
        }
    }
    fclose($handle);
};
echo json_encode($countryR0list);

file_put_contents("./public/data/worldR0.json", json_encode($countryR0list));

?>