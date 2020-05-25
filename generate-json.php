<?php

class country {}

/**
 * Get a list of ISO 2 letter country codes
 */
$row = 1;
$countryCodes = array();
if (($handle = fopen("./rawdata/ISO-countries.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
        $countryCodes[] = array($row,$data[0],$data[1],$data[2]);
    }
    fclose($handle);
};


/**
 * Add the ISO country code to our r0 list
 */
$countryR0list = array();
if (($handle = fopen("./rawdata/world-r0.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
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

/**
 * Add the ISO country code to our list of currently active cases
 */
$countryActivelist = array();
if (($handle = fopen("./rawdata/world-active.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($countryCodes as $countrycode) {
            if ($countrycode[2]==$data[1]) {
                $country = new country;
                $country->id = $countrycode[1];
                $country->nr = $data[0];
                $country->value = round(100000 * $data[2]/$countrycode[3], 1);
                $countryActivelist[] = $country;
                $found = TRUE;
            }
        }
        if (!$found) {
         //   echo "<br/>Not found: " . $data[1];
        }
    }
    fclose($handle);
};

/**
 * Add the ISO country code to our list of cumulative cases
 */
$countryCumulativelist = array();
if (($handle = fopen("./rawdata/world-cumulative.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($countryCodes as $countrycode) {
            if ($countrycode[2]==$data[1]) {
                $country = new country;
                $country->id = $countrycode[1];
                $country->nr = $data[0];
                $country->value = round(100000 * $data[2]/$countrycode[3], 1);
                $countryCumulativelist[] = $country;
                $found = TRUE;
            }
        }
        if (!$found) {
         //   echo "<br/>Not found: " . $data[1];
        }
    }
    fclose($handle);
};


/**
 * Add the ISO country code to our list of deaths
 */
$countryDeathslist = array();
if (($handle = fopen("./rawdata/world-deaths.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($countryCodes as $countrycode) {
            if ($countrycode[2]==$data[1]) {
                $country = new country;
                $country->id = $countrycode[1];
                $country->nr = $data[0];
                $country->value = round(100000 * $data[2]/$countrycode[3], 1);
                $countryDeathslist[] = $country;
                $found = TRUE;
            }
        }
        if (!$found) {
         //   echo "<br/>Not found: " . $data[1];
        }
    }
    fclose($handle);
};

/**
 * Add the ISO country code to our list of recovered cases
 */
$countryRecoveredlist = array();
if (($handle = fopen("./rawdata/world-recovered.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($countryCodes as $countrycode) {
            if ($countrycode[2]==$data[1]) {
                $country = new country;
                $country->id = $countrycode[1];
                $country->nr = $data[0];
                $country->value = round(100000 * $data[2]/$countrycode[3], 1);
                $countryRecoveredlist[] = $country;
                $found = TRUE;
            }
        }
        if (!$found) {
         //   echo "<br/>Not found: " . $data[1];
        }
    }
    fclose($handle);
};

/**
 * Add the ISO country code to our list of new active cases
 */
$countryActiveDifflist = array();
if (($handle = fopen("./rawdata/world-active-diff.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($countryCodes as $countrycode) {
            if ($countrycode[2]==$data[1]) {
                $country = new country;
                $country->id = $countrycode[1];
                $country->nr = $data[0];
                $country->value = round(100000 * $data[2]/$countrycode[3], 1);
                $countryActiveDifflist[] = $country;
                $found = TRUE;
            }
        }
        if (!$found) {
         //   echo "<br/>Not found: " . $data[1];
        }
    }
    fclose($handle);
};

/**
 * Add the ISO country code to our list of new deaths
 */
$countryDeathsDifflist = array();
if (($handle = fopen("./rawdata/world-deaths-diff.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($countryCodes as $countrycode) {
            if ($countrycode[2]==$data[1]) {
                $country = new country;
                $country->id = $countrycode[1];
                $country->nr = $data[0];
                $country->value = round(100000 * $data[2]/$countrycode[3], 1);
                $countryDeathsDifflist[] = $country;
                $found = TRUE;
            }
        }
        if (!$found) {
         //   echo "<br/>Not found: " . $data[1];
        }
    }
    fclose($handle);
};


//echo json_encode($countryR0list);

/**
 * Write the json files
 */
file_put_contents("./public/data/world-R0.json", json_encode($countryR0list));
file_put_contents("./public/data/world-active.json", json_encode($countryActivelist));
file_put_contents("./public/data/world-cumulative.json", json_encode($countryCumulativelist));
file_put_contents("./public/data/world-deaths.json", json_encode($countryDeathslist));
file_put_contents("./public/data/world-recovered.json", json_encode($countryRecoveredlist));
file_put_contents("./public/data/world-active-diff.json", json_encode($countryActiveDifflist));
file_put_contents("./public/data/world-deaths-diff.json", json_encode($countryDeathsDifflist));

file_put_contents("./dist/data/world-R0.json", json_encode($countryR0list));
file_put_contents("./dist/data/world-active.json", json_encode($countryActivelist));
file_put_contents("./dist/data/world-cumulative.json", json_encode($countryCumulativelist));
file_put_contents("./dist/data/world-deaths.json", json_encode($countryDeathslist));
file_put_contents("./dist/data/world-recovered.json", json_encode($countryRecoveredlist));
file_put_contents("./dist/data/world-active-diff.json", json_encode($countryActiveDifflist));
file_put_contents("./dist/data/world-deaths-diff.json", json_encode($countryDeathsDifflist));


?>