<?php

namespace Mantra;

use Nette\Object;
use Phongo\DateTime;
use Phongo\Reference;
use MongoId;


/**
 * Translates JSON and 10gen into structure of Mongo/Phongo objects
 */
class Decoder extends Object {
    
    
    public function decode($data) {
        
        if (is_array($data)) {
            $tmp = array();
            foreach ($data as $key => $val) {
                $tmp[$key] = $this->decode($val);
            }
            return $tmp;
        } elseif (is_string($data)) {
            // integer/float
            if (is_numeric($data)) {
                ///
                
                return (float)$data;
            }
            
            // DateTime
            if (preg_match('/(?:^|Date\()?(\d{4}-\d{2}-\d{2})(?:[T ](\d{2}:\d{2}:\d{2})(?:\.(\d+))?)(?:\)|$)/', $data, $match)) {
                $date = new DateTime("$match[1] $match[2]");
                if (!empty($match[3])) $date->usec = (int) str_pad($match[3], 6, '0');
                
                return $date;
            }
            
            // ObjectId
            if (preg_match('/ObjectId\("?([0-9a-fA-F]{24})"?\)/', $data, $match)) {
                $id = new MongoId($match[1]);
                return $id;
            }
            
            // Reference Dbref("collection", "id", "database")
            if (preg_match('/^Dbref\(\s*"?([^"]+)"?\s*,\s*"?([0-9a-fA-F]{24})"?\s*(?:,\s*"?([^"]+)"?\s*)?\)$/', $data, $match) {
                $ref = new Reference($match[1], $match[2], $match[3]);
                return $ref;
            }
            
            ///
        } elseif (is_numeric($data)) {
            ///
        } else {
            ///
        }
        
    }
    
}
