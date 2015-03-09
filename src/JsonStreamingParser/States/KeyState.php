<?php namespace JsonStreamingParser\States;

use JsonStreamingParser\Consumers\Consumer;
use JsonStreamingParser\Consumers\EscapeConsumer;

class KeyState extends BaseState {
	/**
	 * @var Consumer
	 */
	protected $consumer;

	protected $buffer;
	protected $done = false;

	public function __destruct() {
		$this->listener->key( $this->buffer );
		$this->addState( ValueWrapperState::class );
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
			$this->done = true;
		}
		elseif($this->done && $this->whitespace($c)) {
			return;
		}
		elseif( $c === ':' && $this->done ) {
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
