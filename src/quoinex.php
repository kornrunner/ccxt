<?php

namespace ccxt;

use Exception as Exception; // a common import

class quoinex extends liquid {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'quoinex',
            'name' => 'QUOINEX',
        ));
    }
}
