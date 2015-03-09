<?php namespace JsonStreamingParser\States;

use JsonStreamingParser_Listener;
use SplStack;

class ObjectState extends ValueState {
	public function __construct( JsonStreamingParser_Listener $listener, SplStack  $stack ) {
		parent::__construct( $listener, $stack );
		$this->listener->start_object();
	}

	public function __destruct() {
		$this->listener->end_object();
		parent::__destruct();
	}

	public function addChar( $c ) {
		if( $c === '}' ) {
			$this->popState();
		}
		elseif( $c === '"' ) {
			$this->addState( KeyState::class );
		}
		elseif( $c === ',' || $this->whitespace($c) ) {
		}
		else {
			throw new StateException( 'Start of string expected for object key. Instead got: ' . $c );
		}
	}
}
