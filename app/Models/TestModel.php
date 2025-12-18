<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    // Unused private field - should trigger UnusedPrivateField
    private $unusedField = 'test';
    
    // Short variable names - should trigger ShortVariable (if enabled)
    public function badMethod($a, $b, $c)
    {
        // Cyclomatic complexity - nested conditions
        if ($a > 0) {
            if ($b > 0) {
                if ($c > 0) {
                    if ($a > $b) {
                        if ($b > $c) {
                            if ($a > 10) {
                                if ($b > 5) {
                                    return $a + $b + $c;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // Long method with too many lines
        $result = 0;
        $result += 1;
        $result += 2;
        $result += 3;
        $result += 4;
        $result += 5;
        $result += 6;
        $result += 7;
        $result += 8;
        $result += 9;
        $result += 10;
        $result += 11;
        $result += 12;
        $result += 13;
        $result += 14;
        $result += 15;
        $result += 16;
        $result += 17;
        $result += 18;
        $result += 19;
        $result += 20;
        
        return $result;
    }
    
    // Method with too many parameters - should trigger TooManyFields/ExcessiveParameterList
    public function tooManyParams($param1, $param2, $param3, $param4, $param5, $param6, $param7, $param8)
    {
        return $param1 + $param2 + $param3 + $param4 + $param5 + $param6 + $param7 + $param8;
    }
    
    // Unused parameter - should trigger UnusedFormalParameter
    public function unusedParam($used, $unused)
    {
        return $used * 2;
    }
    
    // Boolean flag parameter - should trigger BooleanArgumentFlag
    public function booleanFlag($data, $isActive = true)
    {
        if ($isActive) {
            return $data;
        }
        return null;
    }
}