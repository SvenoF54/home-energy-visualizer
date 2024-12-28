<?php

class StringHelper {
    public static function convertToDateTime($input, $seconds = null) {
        $dt = new DateTime();
        try {
            $dt = new DateTime($input);
        } catch (Exception $e) {
            $dt = new DateTime();
        }
        return $seconds == null ? $dt->format('Y-m-d H:i:s') : $dt->format("Y-m-d H:i:$seconds");
    }

    public static function convertToDate($input) {
        $dt = new DateTime();
        try {
            $dt = new DateTime($input);
        } catch (Exception $e) {
            $dt = new DateTime();
        }
        return $dt->format('Y-m-d') ;
    }

    public static function formGetString($keyname, $default = null) {
        if (! isset($_REQUEST[$keyname])) return $default;
        return filter_var($_REQUEST[$keyname], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    public static function formGetBool($keyname, $default = null) {
        if (! isset($_REQUEST[$keyname])) return $default;
        $val = filter_var($_REQUEST[$keyname], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return $val == "true";
    }

    public static function formGetInt($keyname, $default = 0) {
        if (! isset($_REQUEST[$keyname])) return $default;
        return (int) filter_var($_REQUEST[$keyname], FILTER_SANITIZE_NUMBER_INT);
    }

    public static function formGetDateTime($keyname, $seconds) {
        if (! isset($_REQUEST[$keyname])) return date("Y-m-d H:i:$seconds");
        $val = filter_var($_REQUEST[$keyname], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return self::convertToDateTime($val, $seconds);
    } 

    public static function formGetDate($keyname, $default = null) {
        if (! isset($_REQUEST[$keyname])) return date('Y-m-d', $default);
        $val = filter_var($_REQUEST[$keyname], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return self::convertToDate($val);
    }

    public static function formGetMonthYear($keyname, $default = null) {
        if (! isset($_REQUEST[$keyname])) return $default;
        
        $value = filter_var($_REQUEST[$keyname], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $timestampRegex = '/^\d{4}-\d{2}$/'; // YYYY-MM Format
        if (preg_match($timestampRegex, $value)) return $value;
        
        return $default;
    }

    public static function formGetFloat($keyname, $default = null) {
        if (! isset($_REQUEST[$keyname])) return $default;
        $tempVal = str_replace(".", ",", $_REQUEST[$keyname]);
        $value = filter_var($tempVal, FILTER_VALIDATE_FLOAT, array('options' => array('decimal' => ',', 'min'=> -1000, 'max' => 1000)));        
        
        return $value;
    }

    public static function formGetStringArray($keyname, array $validValues, array $default = array()) : array {
        if (! isset($_REQUEST[$keyname]) || ! is_array($_REQUEST[$keyname])) return $default;
        $inputValues = $_REQUEST[$keyname];
        $resultValues = [];
        foreach ($inputValues as $inputValue) {
            if (in_array($inputValue, $validValues)) {
                $resultValues[] = $inputValue;
            }
        }        
        return $resultValues;

    }

    public static function formatEnergyInWatt($val, $suffix="") {
        
        if (abs($val) > 1000000) {
            return number_format($val / 1000000, 2, ',', '.')." mW".$suffix;            
        } elseif (abs($val) > 1000) {
            return number_format($val / 1000, 2, ',', '.')." kW".$suffix;            
        } else {
            return ($val > 0 | $val < 0 ? $val : 0)." W".$suffix;
        }        
    }
    
    public static function formatNumberOrNull($val)
    {                
        if ($val == null) return null;
        return round((float)$val, 0); // Bug in PHP, with values after comma -> 71,09887768865
    }

    public static function formatNumber($val, $digits=0)
    {
        $formattedValue = rtrim(rtrim(number_format($val, $digits, ',', '.'), '0'), ',');
        return $formattedValue;
    }

    public static function formatCurrency($priceInCent)
    {        
        return number_format($priceInCent/100, 2, ',', '.')."€";
    }

    public static function formatCentCurrency($priceInCent)
    {        
        return StringHelper::formatNumber($priceInCent, 5)." Ct";
    }

}
