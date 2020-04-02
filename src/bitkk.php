<?php

namespace ccxt;

use Exception; // a common import

class bitkk extends zb {

    public function describe() {
        return $this->deep_extend(parent::describe (), array(
            'id' => 'bitkk',
            'name' => 'bitkk',
            'comment' => 'a Chinese ZB clone',
            'urls' => array(
                'api' => array(
                    'public' => 'http://api.bitkk.com/data', // no https for public API
                    'private' => 'https://trade.bitkk.com/api',
                ),
                'www' => 'https://www.bitkk.com',
                'doc' => 'https://www.bitkk.com/i/developer',
                'fees' => 'https://www.bitkk.com/i/rate',
            ),
        ));
    }
}
