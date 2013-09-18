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

class An_Eccube_Transaction {
    protected $query;
    protected $alive = false;
    
    public function __construct(SC_Query_Ex $query) {
        $this->query = $query;
        $this->begin();
    }
    
    protected function begin() {
        $mdb2 = An_Eccube_Utils::getMDB2($this->query);
        $result = $mdb2->beginNestedTransaction();
        if (PEAR::isError($result)) {
            throw new RuntimeException($result->toString());
        }

        $this->alive = true;
    }
    
    public function commit() {
        if (!$this->alive) {
            throw new RuntimeException('Failed to rollback. transaction was already closed.');
        }
        
        $mdb2 = An_Eccube_Utils::getMDB2($this->query);
        $result = $mdb2->completeNestedTransaction();
        if (PEAR::isError($result)) {
            throw new RuntimeException($result->toString());
        }
        
        $this->alive = false;
    }
    
    public function rollback() {
        if (!$this->alive) {
            throw new RuntimeException('Failed to rollback. transaction was already closed.');
        }
 
        $mdb2 = An_Eccube_Utils::getMDB2($this->query);
        $result = $mdb2->failNestedTransaction();
        if (PEAR::isError($result)) {
            throw new RuntimeException($result->toString());
        }
        
        $this->alive = false;
    }
    
    public function isAlive() {
        return $this->alive;
    }
}
