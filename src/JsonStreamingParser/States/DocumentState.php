<?php namespace JsonStreamingParser\States;

use JsonStreamingParser\MiniStack;
use JsonStreamingParser_Listener;

class DocumentState extends BaseState {

	public function __construct( JsonStreamingParser_Listener $listener, MiniStack $stack ) {
		parent::__construct( $listener, $stack );
		$this->listener->start_document();
	}

	public function __destruct() {
		$this->listener->end_document();
		parent::__destruct();
	}

	public function addChar( $c ) {
		if( $this->whitespace( $c ) ) {
			return;
		}
		elseif( $c !== '[' && $c !== '{' ) {
			throw new StateException( 'Document must start with object or array.' );
		}
		else {
			$state = $this->addState( ValueWrapperState::class );
			$state->addChar( $c );
		}
	}
}
