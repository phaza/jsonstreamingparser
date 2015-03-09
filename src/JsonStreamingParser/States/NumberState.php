<?php namespace JsonStreamingParser\States;

class NumberState extends ValueState {

	protected $buffer = '';

	public function __destruct() {
		$val = strpos( $this->buffer, '.' ) !== false ? floatval( $this->buffer ) : intval( $this->buffer );
		$this->listener->value( $val );
		parent::__destruct();
	}

	public function addChar( $c ) {
		if( ctype_digit( $c ) || (empty($this->buffer) && $c === '-') ) {
			$this->buffer .= $c;
		}
		elseif( $c === '.' ) {
			if( strpos( $this->buffer, '.' ) !== false ) {
				throw new StateException( 'Cannot have multiple decimal points in a number.' );
			}
			elseif( stripos( $this->buffer, 'e' ) !== false ) {
				throw new StateException( 'Cannot have a decimal point in an exponent.' );
			}
			$this->buffer .= $c;
		}
		elseif( $c === 'e' || $c === 'E' ) {
			if( stripos( $this->buffer, 'e' ) !== false ) {
				throw new StateException( 'Cannot have multiple exponents in a number.' );
			}
			$this->buffer .= $c;
		}
		elseif( $c === '+' || $c === '-' ) {
			$last = mb_substr( $this->buffer, -1 );
			if( !( $last === 'e' || $last === 'E' ) ) {
				throw new StateException( 'Can only have "+" or "-" after the "e" or "E" in a number.' );
			}
			$this->buffer .= $c;
		}
		else {
			$this->popState();
			// we have consumed one beyond the end of the number.
			// We know there will be a ValueWrapperState since our parent::__contruct hasn't run yet
			// So we skip to the second last state available
			$this->stack->at(1)->addChar( $c );
		}
	}
}
