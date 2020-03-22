<?php

namespace ccxt;

use Exception; // a common import

class okex3 extends okex {

    public function describe() {
        return array_replace_recursive(parent::describe (), array(
            // this is a stub file that will be removed before 2020 Q2
            // it is placed here for temporary backward compatibility
            'id' => 'okex3',
        ));
    }
}
