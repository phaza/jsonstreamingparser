<?php namespace JsonStreamingParser\States;

class ValueWrapperState extends BaseState {
	public function addChar( $c ) {
		if( $c === '[' ) {
			$this->addState( ArrayState::class );
		}
		elseif( $c === '{' ) {
			$this->addState( ObjectState::class );
		}
		elseif( $c === '"' ) {
			$this->addState( StringState::class );
		}
		elseif( $this->isDigit( $c ) ) {
			$state = $this->addState( NumberState::class );
			$state->addChar( $c );
		}
		elseif( $c === 't' ) {
			$this->addState( TrueState::class );
		}
		elseif( $c === 'f' ) {
			$this->addState( FalseState::class );
		}
		elseif( $c === 'n' ) {
			$this->addState( NullState::class );
		}
		elseif( $this->whitespace( $c ) || $c === ',' || $c === ':' ) {
			return;
		}
		else {
			throw new StateException( 'Unexpected character for value: ' . $c );
		}
	}

}
