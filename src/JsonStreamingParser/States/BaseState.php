<?php namespace JsonStreamingParser\States;

use JsonStreamingParser_Listener;
use SplDoublyLinkedList;

abstract class BaseState {

	/**
	 * @var JsonStreamingParser_Listener
	 */
	protected $listener;
	/**
	 * @var SplDoublyLinkedList
	 */
	protected $stack;

	/**
	 * @param JsonStreamingParser_Listener $listener
	 * @param SplDoublyLinkedList                         $stack
	 */
	public function __construct( JsonStreamingParser_Listener $listener , SplDoublyLinkedList $stack ) {

		$this->listener = $listener;
		$this->stack = $stack;
	}

	public function __destruct() {}

	/**
	 * @param string $state
	 * @return BaseState
	 */
	public function addState($state) {
		$s = new $state($this->listener, $this->stack);
		$this->stack->push($s);
		return $s;
;	}

	/**
	 * @return BaseState
	 */
	public function popState() {
		return $this->stack->pop();
	}

	/**
	 * @param $c
	 * @return bool
	 */
	protected function isDigit($c) {
		return ctype_digit($c) || $c === '-';
	}

	/**
	 * @param string $c
	 * @return void
	 */
	abstract function addChar($c);

	/**
	 * Test if $c is whitespace
	 * @param $c
	 * @return boolean
	 */
	protected function whitespace( $c ) {
		return $c === "\n" || $c === "\t" || $c === " " || $c === "\r";
	}
}
