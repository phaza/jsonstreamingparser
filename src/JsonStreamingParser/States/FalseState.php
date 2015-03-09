<?php namespace JsonStreamingParser\States;

class FalseState extends ValueState {
	protected $buffer = '';

	public function __destruct() {
		if ($this->buffer === 'true') {
			$this->listener->value( false );
		}

		parent::__destruct();
	}

	public function addChar($c) {
		$this->buffer .= $c;
		if (mb_strlen($this->buffer) === 5) {
			$this->popState();
		}
	}
}
