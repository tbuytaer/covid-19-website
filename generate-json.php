<?php

class country {};
class state {};
class datapoint {};

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
 * Get a list of ISO 2 letter US state codes
 */
$row = 1;
$stateCodes = array();
if (($handle = fopen("./rawdata/ISO-US-states.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
        $stateCodes[] = array($row,$data[0],$data[1],$data[2]);
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
            if ($countrycode[2] == $data[1]) {
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
 * Add the ISO state code to our r0 list
 */
$stateR0list = array();
if (($handle = fopen("./rawdata/US-r0.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($stateCodes as $statecode) {
            if ($statecode[2] == $data[1]) {
                $state = new state;
                $state->id = $statecode[1];
                $state->nr = $data[0];
                $state->value = $data[2];
                $stateR0list[] = $state;
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
            if ($countrycode[2] == $data[1]) {
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
 * Add the ISO state code to our list of currently active cases
 */
$stateActivelist = array();
if (($handle = fopen("./rawdata/US-active.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($stateCodes as $statecode) {
            if ($statecode[2] == $data[1]) {
                $state = new state;
                $state->id = $statecode[1];
                $state->nr = $data[0];
                $state->value = round(100000 * $data[2]/$statecode[3], 1);
                $stateActivelist[] = $state;
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
            if ($countrycode[2] == $data[1]) {
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
 * Add the ISO state code to our list of cumulative cases
 */
$stateCumulativelist = array();
if (($handle = fopen("./rawdata/US-cumulative.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($stateCodes as $statecode) {
            if ($statecode[2] == $data[1]) {
                $state = new state;
                $state->id = $statecode[1];
                $state->nr = $data[0];
                $state->value = round(100000 * $data[2]/$statecode[3], 1);
                $stateCumulativelist[] = $state;
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
            if ($countrycode[2] == $data[1]) {
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
 * Add the ISO state code to our list of deaths
 */
$stateDeathslist = array();
if (($handle = fopen("./rawdata/US-deaths.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($stateCodes as $statecode) {
            if ($statecode[2] == $data[1]) {
                $state = new state;
                $state->id = $statecode[1];
                $state->nr = $data[0];
                $state->value = round(100000 * $data[2]/$statecode[3], 1);
                $stateDeathslist[] = $state;
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
            if ($countrycode[2] == $data[1]) {
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
 * Add the ISO state code to our list of recovered cases
 */
$stateRecoveredlist = array();
if (($handle = fopen("./rawdata/US-recovered.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($stateCodes as $statecode) {
            if ($statecode[2] == $data[1]) {
                $state = new state;
                $state->id = $statecode[1];
                $state->nr = $data[0];
                $state->value = round(100000 * $data[2]/$statecode[3], 1);
                $stateRecoveredlist[] = $state;
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
            if ($countrycode[2] == $data[1]) {
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
 * Add the ISO state code to our list of new active cases
 */
$stateActiveDifflist = array();
if (($handle = fopen("./rawdata/US-active-diff.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($stateCodes as $statecode) {
            if ($statecode[2] == $data[1]) {
                $state = new state;
                $state->id = $statecode[1];
                $state->nr = $data[0];
                $state->value = round(100000 * $data[2]/$statecode[3], 1);
                $stateActiveDifflist[] = $state;
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
            if ($countrycode[2] == $data[1]) {
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

/**
 * Add the ISO state code to our list of new deaths
 */
$stateDeathsDifflist = array();
if (($handle = fopen("./rawdata/US-deaths-diff.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($stateCodes as $statecode) {
            if ($statecode[2] == $data[1]) {
                $state = new state;
                $state->id = $statecode[1];
                $state->nr = $data[0];
                $state->value = round(100000 * $data[2]/$statecode[3], 1);
                $stateDeathsDifflist[] = $state;
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
 * Add the ISO country code to our list of risk
 */
$countryRisklist = array();
if (($handle = fopen("./rawdata/world-risk.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($countryCodes as $countrycode) {
            if ($countrycode[2] == $data[1]) {
                $country = new country;
                $country->id = $countrycode[1];
                $country->nr = $data[0];
                $country->value = round(100000 * $data[2]/$countrycode[3], 2);
                $countryRisklist[] = $country;
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
 * Add the ISO state code to our list of risk
 */
$stateRisklist = array();
if (($handle = fopen("./rawdata/US-risk.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($stateCodes as $statecode) {
            if ($statecode[2] == $data[1]) {
                $state = new state;
                $state->id = $statecode[1];
                $state->nr = $data[0];
                $state->value = round(100000 * $data[2]/$statecode[3], 2);
                $stateRisklist[] = $state;
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
 * Add the ISO country code to our list of CFR
 */
$countryCFRlist = array();
if (($handle = fopen("./rawdata/world-CFR.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($countryCodes as $countrycode) {
            if ($countrycode[2] == $data[1]) {
                $country = new country;
                $country->id = $countrycode[1];
                $country->nr = $data[0];
                if ($data[3] != "Indeterminate" && $data[3] <= 0.01) {
                    $country->value = round(100 * $data[2],1);
                    $countryCFRlist[] = $country;
                }
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
 * Add the ISO state code to our list of CFR
 */
$stateCFRlist = array();
if (($handle = fopen("./rawdata/US-CFR.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($stateCodes as $statecode) {
            if ($statecode[2] == $data[1]) {
                $state = new state;
                $state->id = $statecode[1];
                $state->nr = $data[0];
                if ($data[3] != "Indeterminate" && $data[3] <= 0.01) {
                    $state->value = round(100 * $data[2],1);
                    $stateCFRlist[] = $state;
                }
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
 * Add the ISO country code to our country list
 */
$countryIDlist = array();
if (($handle = fopen("./rawdata/country-general.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $found = FALSE;
        foreach ($countryCodes as $countrycode) {
            if ($countrycode[2]==$data[1]) {
                $country = new country;
                $country->id = $countrycode[1];
                $country->nr = $data[0];
                $countryIDlist[] = $country;
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
 * Convert country specific CSV files to JSON format
 */
$directory = './rawdata';
$scanned_directory = array_diff(scandir($directory), array('..', '.'));
foreach ($scanned_directory as $filenr => $filename) {
    if(strpos($filename,'country-') === 0) {
        if(strpos($filename, 'country-general') === 0) {
            // Skip this file
        } else if(strpos($filename,'-jh-')) {
            // File contains only 1 column with values
            $csv = file_get_contents('./rawdata/'.$filename);
            $array = array_map("str_getcsv", explode("\n", $csv));
            $dataPointList = [];
            foreach($array as $key=>$value) {
               // echo json_encode($value);
                $datapoint = new datapoint;
                $datapoint->date = date("Y-m-d", mktime(12,0,0,1,21 + $key,2020));
                $datapoint->value = $value[0];
                $dataPointList[]=$datapoint;
            }
            file_put_contents("./public/data/".basename($filename, ".csv").".json", json_encode($dataPointList));
            file_put_contents("./dist/data/".basename($filename, ".csv").".json", json_encode($dataPointList));
        } else {
            // File contains 2 columns with day and values
            $csv = file_get_contents('./rawdata/'.$filename);
            $array = array_map("str_getcsv", explode("\n", $csv));
            $dataPointList = [];
            foreach($array as $key=>$value) {
                //echo json_encode($value);
                $datapoint = new datapoint;
                $datapoint->date = date("Y-m-d", mktime(12,0,0,1,21 + $value[0],2020));
                $datapoint->value = round($value[1],2);
                $dataPointList[]=$datapoint;
            }
            file_put_contents("./public/data/".basename($filename, ".csv").".json", json_encode($dataPointList));
            file_put_contents("./dist/data/".basename($filename, ".csv").".json", json_encode($dataPointList));
        }
    }
}

/**
 * Convert state specific CSV files to JSON format
 */
$directory = './rawdata';
$scanned_directory = array_diff(scandir($directory), array('..', '.'));
foreach ($scanned_directory as $filenr => $filename) {
    if(strpos($filename,'state-') === 0) {
        if(strpos($filename, 'state-general') === 0) {
            // Skip this file
        } else if(strpos($filename,'-jh-')) {
            // File contains only 1 column with values
            $csv = file_get_contents('./rawdata/'.$filename);
            $array = array_map("str_getcsv", explode("\n", $csv));
            $dataPointList = [];
            foreach($array as $key=>$value) {
               // echo json_encode($value);
                $datapoint = new datapoint;
                $datapoint->date = date("Y-m-d", mktime(12,0,0,1,21 + $key,2020));
                $datapoint->value = $value[0];
                $dataPointList[]=$datapoint;
            }
            file_put_contents("./public/data/".basename($filename, ".csv").".json", json_encode($dataPointList));
            file_put_contents("./dist/data/".basename($filename, ".csv").".json", json_encode($dataPointList));
        } else {
            // File contains 2 columns with day and values
            $csv = file_get_contents('./rawdata/'.$filename);
            $array = array_map("str_getcsv", explode("\n", $csv));
            $dataPointList = [];
            foreach($array as $key=>$value) {
                //echo json_encode($value);
                $datapoint = new datapoint;
                $datapoint->date = date("Y-m-d", mktime(12,0,0,1,21 + $value[0],2020));
                $datapoint->value = round($value[1],2);
                $dataPointList[]=$datapoint;
            }
            file_put_contents("./public/data/".basename($filename, ".csv").".json", json_encode($dataPointList));
            file_put_contents("./dist/data/".basename($filename, ".csv").".json", json_encode($dataPointList));
        }
    }
}


/**
 * Write the json files
 */
// For World Map and US Map
file_put_contents("./public/data/world-R0.json", json_encode($countryR0list));
file_put_contents("./public/data/world-active.json", json_encode($countryActivelist));
file_put_contents("./public/data/world-cumulative.json", json_encode($countryCumulativelist));
file_put_contents("./public/data/world-deaths.json", json_encode($countryDeathslist));
file_put_contents("./public/data/world-recovered.json", json_encode($countryRecoveredlist));
file_put_contents("./public/data/world-active-diff.json", json_encode($countryActiveDifflist));
file_put_contents("./public/data/world-deaths-diff.json", json_encode($countryDeathsDifflist));
file_put_contents("./public/data/world-risk.json", json_encode($countryRisklist));
file_put_contents("./public/data/world-CFR.json", json_encode($countryCFRlist));

file_put_contents("./public/data/US-R0.json", json_encode($stateR0list));
file_put_contents("./public/data/US-active.json", json_encode($stateActivelist));
file_put_contents("./public/data/US-cumulative.json", json_encode($stateCumulativelist));
file_put_contents("./public/data/US-deaths.json", json_encode($stateDeathslist));
file_put_contents("./public/data/US-active-diff.json", json_encode($stateActiveDifflist));
file_put_contents("./public/data/US-deaths-diff.json", json_encode($stateDeathsDifflist));
file_put_contents("./public/data/US-risk.json", json_encode($stateRisklist));
file_put_contents("./public/data/US-CFR.json", json_encode($stateCFRlist));


// Also put them in the dist/ folder, but when Vue is compiled these will be overwritten by the ones in public/
file_put_contents("./dist/data/world-R0.json", json_encode($countryR0list));
file_put_contents("./dist/data/world-active.json", json_encode($countryActivelist));
file_put_contents("./dist/data/world-cumulative.json", json_encode($countryCumulativelist));
file_put_contents("./dist/data/world-deaths.json", json_encode($countryDeathslist));
file_put_contents("./dist/data/world-recovered.json", json_encode($countryRecoveredlist));
file_put_contents("./dist/data/world-active-diff.json", json_encode($countryActiveDifflist));
file_put_contents("./dist/data/world-deaths-diff.json", json_encode($countryDeathsDifflist));
file_put_contents("./dist/data/world-risk.json", json_encode($countryRisklist));
file_put_contents("./dist/data/world-CFR.json", json_encode($countryCFRlist));

file_put_contents("./dist/data/US-R0.json", json_encode($stateR0list));
file_put_contents("./dist/data/US-active.json", json_encode($stateActivelist));
file_put_contents("./dist/data/US-cumulative.json", json_encode($stateCumulativelist));
file_put_contents("./dist/data/US-deaths.json", json_encode($stateDeathslist));
file_put_contents("./dist/data/US-active-diff.json", json_encode($stateActiveDifflist));
file_put_contents("./dist/data/US-deaths-diff.json", json_encode($stateDeathsDifflist));
file_put_contents("./dist/data/US-risk.json", json_encode($stateRisklist));
file_put_contents("./dist/data/US-CFR.json", json_encode($stateCFRlist));


// Country list
file_put_contents("./public/data/country-ID.json", json_encode($countryIDlist));

?>