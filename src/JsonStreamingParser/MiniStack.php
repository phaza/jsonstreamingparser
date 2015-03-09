<?php namespace JsonStreamingParser;

class MiniStack {

	/**
	 * @var array
	 */
	protected $stack = [];

	public $last = null;
	private $count = 0;

	public function push($item) {
		$this->last = $item;
		array_unshift($this->stack, $item);
		$this->count++;
	}

	public function pop() {
		$item = array_shift($this->stack);
		$this->count--;
		$this->last = $this->count > 0 ? $this->stack[0]: null;
		return $item;
	}

	public function at($idx) {
		return $this->stack[$idx];
	}
}
