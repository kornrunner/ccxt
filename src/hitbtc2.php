<?php

namespace ccxt;

use Exception; // a common import

class hitbtc2 extends hitbtc {

    public function describe() {
        // this is a temporary stub for backward compatibility
        // https://github.com/ccxt/ccxt/issues/6678
        return $this->deep_extend(parent::describe (), array(
            'id' => 'hitbtc2',
        ));
    }
}
