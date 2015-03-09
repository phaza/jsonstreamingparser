<?php namespace JsonStreamingParser\States;

use JsonStreamingParser_Listener;
use SplStack;

class ArrayState extends ValueState {
	public function __construct( JsonStreamingParser_Listener $listener, SplStack  $stack ) {
		parent::__construct( $listener, $stack );
		$this->listener->start_array();
	}

	public function __destruct() {
		$this->listener->end_array();
		parent::__destruct();
	}


	public function addChar($c) {
		if($c === ']') {
			$this->popState();
		}
		elseif($c === ',') {
			$this->addState(ValueWrapperState::class);
		}
		elseif($this->whitespace($c)) {
			return;
		}
		else {
			$state = $this->addState(ValueWrapperState::class);
			$state->addChar($c);
		}
	}

}
