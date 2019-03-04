<?php

namespace ccxt;

use Exception as Exception; // a common import

class kucoin2 extends kucoin {

    public function describe () {
        // KuCoin v1 is deprecated, 'kucoin2' renamed to 'kucoin', 'kucoin2' to be removed on 2019-03-30
        return array_replace_recursive (parent::describe (), array (
            'id' => 'kucoin2',
        ));
    }
}
