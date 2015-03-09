<?php namespace JsonStreamingParser\States;

class TrueState extends ValueState {

	protected $buffer = '';

	public function __destruct() {
		if ($this->buffer === 'true') {
			$this->listener->value( true );
		}

		parent::__destruct();
	}

	public function addChar($c) {
		$this->buffer .= $c;
		if (mb_strlen($this->buffer) === 4) {
			$this->popState();
		}
	}
}
