<?php

namespace ccxt;

use Exception as Exception; // a common import

class qryptos extends liquid {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'qryptos',
            'name' => 'QRYPTOS',
        ));
    }
}
