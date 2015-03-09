<?php namespace JsonStreamingParser\Consumers;

use JsonStreamingParser\States\StateException;

class EscapeConsumer implements Consumer {

	protected $done   = false;
	protected $buffer = '';

	/**
	 * @var Consumer
	 */
	protected $consumer;


	public function isDone() {
		return $this->done;
	}

	public function getValue() {
		return $this->buffer;
	}

	public function addChar( $c ) {

		if( $this->consumer ) {
			$this->consumer->addChar( $c );

			if( $this->consumer->isDone() ) {
				$this->buffer .= $this->consumer->getValue();
				$this->consumer = null;
				$this->done     = true;
			}

			return;
		}

		if( $c === '"' || $c === '\\' || $c === '/' ) {
			$this->buffer .= $c;
			$this->done = true;
		}
		elseif( $c === 'b' ) {
			$this->buffer .= "\x08";
			$this->done = true;
		}
		elseif( $c === 'f' ) {
			$this->buffer .= "\f";
			$this->done = true;
		}
		elseif( $c === 'n' ) {
			$this->buffer .= "\n";
			$this->done = true;
		}
		elseif( $c === 'r' ) {
			$this->buffer .= "\r";
			$this->done = true;
		}
		elseif( $c === 't' ) {
			$this->buffer .= "\t";
			$this->done = true;
		}
		elseif( $c === 'u' ) {
			$this->consumer = new UnicodeConsumer();
		}
		else {
			throw new StateException( 'Expected escaped character after backslash. Got: ' . $c );
		}
	}
}
