<?php
use JsonStreamingParser\MiniStack;
use JsonStreamingParser\States\DocumentState;

require_once 'ParsingError.php';
require_once 'Listener.php';

class JsonStreamingParser_Parser {
	/**
   * @var MiniStack
   */
  private $_stack;

  private $_stream;

  /**
   * @var JsonStreamingParser_Listener
   */
  private $_listener;

  private $_buffer_size;

  private $_char_number;


  public function __construct($stream, $listener) {
    if (!is_resource($stream) || get_resource_type($stream) != 'stream') {
      throw new InvalidArgumentException("Argument is not a stream");
    }
    if (!($listener instanceof JsonStreamingParser_Listener)) {
      throw new InvalidArgumentException("Listener must implement JsonStreamingParser_Listener");
    }

    $this->_stream = $stream;
    $this->_listener = $listener;

    $this->_stack = new MiniStack();
    $this->_stack->push(new DocumentState($this->_listener, $this->_stack));

    $this->_buffer_size = 8192;
  }


  public function parse() {
    $this->_char_number = 1;
    $eof = false;

    while (!feof($this->_stream) && !$eof) {
      $pos = ftell($this->_stream);
      $line = fread($this->_stream, $this->_buffer_size);
      $ended = (bool)(ftell($this->_stream) - strlen($line) - $pos);
      // if we're still at the same place after stream_get_line, we're done
      $eof = ftell($this->_stream) == $pos;

      $byteLen = strlen($line);
      for ($i = 0; $i < $byteLen; $i++) {
        $this->_consume_char($line[$i]);
        $this->_char_number++;
      }

      if ($ended) {
        $this->_char_number = 1;
      }

    }

    $this->_stack->pop();
  }

  private function _consume_char($c) {
    try {
      $this->_stack->last->addChar($c);
    }
    catch(Exception $e) {
      throw new JsonStreamingParser_ParsingError(0, $this->_char_number, $e->getMessage());
    }
  }
}
