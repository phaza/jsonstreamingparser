<?php namespace JsonStreamingParser\Consumers;

interface Consumer {


	public function addChar($c);

	public function getValue();

	function isDone();
}
