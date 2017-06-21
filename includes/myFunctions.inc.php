<?php

// defining localtime
function getLocalTime($ts, $offset) {
    return ($ts - date("Z", $ts)) + (3600 * $offset);
}

// converting date into bangla
function getBanglaDate($inputDate, $time = false, $day = true){
    $dayAndMonth = array('Saturday'=>'শনিবার', 'Sunday'=>'রবিবার', 'Monday'=>'সোমবার', 'Tuesday'=>'মঙ্গলবার', 'Wednesday'=>'বুধবার', 'Thursday'=>'বৃহস্পতিবার', 'Friday'=>'শুক্রবার', 'January'=>'জানুয়ারি', 'February'=>'ফেব্রুয়ারি', 'March'=>'মার্চ', 'April'=>'এপ্রিল', 'May'=>'মে', 'June'=>'জুন', 'July'=>'জুলাই', 'August'=>'আগস্ট', 'September'=>'সেপ্টেম্বর', 'October'=>'অক্টোবর', 'November'=>'নবেম্বর', 'December'=>'ডিসেম্বর');

    $banglaNumValue = array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');

    $dateFromString = strtotime(str_replace('-', '', $inputDate));
    $outputDate = $time ? date("d F Y H:i", $dateFromString) : ($day ? date("l d F Y", $dateFromString) : date("d m Y", $dateFromString));
    $outputDateArray = explode(' ', $outputDate);
    $banglaDate = '';
    $separator = $day ? ' ' : '/';

    //convert values to bangla
    foreach($outputDateArray as $value){
        if(!is_numeric($value)){
            if(array_key_exists($value, $dayAndMonth)){
                $banglaDate .= $dayAndMonth[$value].' ';
            } else {
                $banglaDateOrMonth = '';
                foreach(str_split($value) as $numValue){
                    $banglaDateOrMonth .= array_key_exists($numValue, $banglaNumValue) ? $banglaNumValue[$numValue] : $numValue;
                }
                $banglaDate .= ' - ' . $banglaDateOrMonth.' ';
            }
        } else {
            $banglaDateOrMonth = '';
            foreach(str_split($value) as $numValue){
                $banglaDateOrMonth .= $banglaNumValue[$numValue];
            }
            $banglaDate .= $banglaDateOrMonth. ' ';
        }
    }
    return $day ? $banglaDate : str_replace(' ', '/', trim($banglaDate));
}

// convert Numerical value in banla
function convertEngNumberInbangla($engValues){
    $banglaValues = array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');
    $separatedValue = str_split($engValues);

    foreach($separatedValue as $value){
        if(array_key_exists($value, $banglaValues)){
            $banglaValue[] = $banglaValues[$value];
        }
        else{
            $banglaValue[] = $value;
        }
    }

    return implode($banglaValue);
}

/* Date format converter */
function dateFormatConverter($inputed_date){
    // String to Array
    if(strpos($inputed_date, '/')){
        $date_array = explode('/', $inputed_date);
        $day = $date_array[0];
        $month = $date_array[1];
        $year = $date_array[2];
        $converted_date = $year.'-'.$month.'-'.$day;
    }

    if(strpos($inputed_date, '-')){
        $date_array = explode('-', $inputed_date);
        $day = $date_array[2];
        $month = $date_array[1];
        $year = $date_array[0];
        $converted_date = $day.'/'.$month.'/'.$year;
    }

    return $converted_date;
}

function nukeMagicQuotes() {
  if (get_magic_quotes_gpc()){
    function stripslashes_deep($value) {
      $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
      return $value;
      }
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    }
}


function make_alias($string, array $remove_characters, $replace_with = '-'){
    // Repeated replace characters
    $repeated_char = $replace_with . $replace_with;
    // remove caracters from string
    $striped_string = str_replace($remove_characters, "", $string);
    
    // repalce blank space with required caracter
    $string_with_dash = str_replace(' ', $replace_with, $striped_string);
    
    // remove repeated replaceable characters if nay
//    $string_with_dash = str_replace($repeated_char, $replace_with, $string_with_dash);
//    if(preg_match("/$repeated_char/", $string_with_dash)){
//        $string_with_dash = make_alias($string_with_dash, $remove_characters);
//    }
    return $string_with_dash;
}

function get_id($url, $delimiter, $from_where = 'start'){
    $delimiter_position = $from_where == 'end' ? strrpos($url, $delimiter)+1 : strpos($url, $delimiter);
    $id = $from_where == 'end' ? substr($url, $delimiter_position) : substr($url, 0, $delimiter_position);
    
    return (int)$id;
}

function db_connect($user, $pwd, $db, $host = 'localhost'){
    try {
        return new PDO("mysql:host=$host;dbname=$db", $user, $pwd);
    } catch (PDOException $e) {
        echo $e->getMessage();
    } 
}


function create_cache($cache_name = false, $content = false, $dir = false){
    if($cache_name && $content){
        $cache = new Cache();
        if($dir) $cache->setCachePath($dir);
        $cache->store($cache_name, $content);
		return true;
    } else {
        return false;
    }
}


function get_cache($cache_name = false, $dir = false){
    if($cache_name){
        $cache = new Cache();
        if($dir) $cache->setCachePath($dir);
        return $cache->retrieve($cache_name);
    } else {
        return false;
    }
}