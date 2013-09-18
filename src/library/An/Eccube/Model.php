<?php
/*
 * EC-CUBEアフィリナビクーポンプラグイン
* Copyright (C) 2013 M-soft All Rights Reserved.
* http://m-soft.jp/
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

abstract class An_Eccube_Model {
    private $stored = false;
    
    /**
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = array(), array $options = array()) {
        $this->query = isset($options['query']) ? $options['query'] : self::getQuery();
        
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    
    public function isStored() {
        return $this->stored;
    }
    
    protected function ensureStored() {
        $this->stored = true;
    }
    
    protected function cancelStored() {
        $this->stored = false;
    }
    
    /**
     * @return array
     */
    protected function getStorableProperties() {
        static $properties;
        
        if ($properties === null) {
            $class = new ReflectionClass($this);
            $properties = array();
            foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $properties[$property->name] = $property->name;
            }
        }
        
        return $properties;
    }
    
    /**
     * 
     * @return array
     */
    protected function toStorableValues() {
        $values = array();
        
        foreach ($this->getStorableProperties() as $name) {
            $values[$name] = $this->$name;
        }
        
        return $values;
    }
    
    /**
     * @return An_Eccube_Transaction
     */
    public static function beginTransaction(SC_Query_Ex $query = null) {
        if (!$query) {
            $query = self::getQuery();
        }
        
        $tx = new An_Eccube_Transaction($query);
        return $tx;
    }

    /**
     * @return SC_Query_Ex
     */
    public static function getQuery() {
        return SC_Query_Ex::getSingletonInstance();
    }
}
