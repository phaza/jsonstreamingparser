<?php namespace JsonStreamingParser\States;

use JsonStreamingParser\Consumers\Consumer;
use JsonStreamingParser\Consumers\EscapeConsumer;

class StringState extends ValueState {


	/**
	 * @var Consumer
	 */
	protected $consumer;

	protected $buffer;

	public function __destruct() {
		$this->listener->value( $this->buffer );
		parent::__destruct();
	}

	public function addChar( $c ) {
		if( $this->consumer ) {
			$this->consumer->addChar( $c );

			if( $this->consumer->isDone() ) {
				$this->buffer .= $this->consumer->getValue();
				$this->consumer = null;
			}

			return;
		}

		if( $c === '"' ) {
			$this->popState();
		}
		elseif( $c === '\\' ) {
			$this->consumer = new EscapeConsumer();
		}
		elseif( ( $c < "\x1f" ) || ( $c === "\x7f" ) ) {
			throw new StateException( 'Unescaped control character encountered: ' . $c );
		}
		else {
			$this->buffer .= $c;
		}
	}
}
