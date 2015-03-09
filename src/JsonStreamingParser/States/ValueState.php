<?php namespace JsonStreamingParser\States;

abstract class ValueState extends BaseState {
	public function __destruct() {
		$this->popState();
		parent::__destruct();
	}
}
