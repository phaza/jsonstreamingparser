<?php namespace JsonStreamingParser\Consumers;

use JsonStreamingParser\States\StateException;

class UnicodeConsumer implements Consumer {

	protected $buffer         = [ ];
	protected $done           = false;
	protected $high_surrogate = 0;
	protected $value;
	protected $escape_buffer  = '';
	protected $isSurrogate    = false;

	public function isDone() {
		return $this->done;
	}

	public function getValue() {
		return $this->value;
	}

	public function addChar( $c ) {
		if( $this->isSurrogate ) {
			$this->escape_buffer .= $c;
			if( mb_strlen( $this->escape_buffer ) === 2 ) {
				if( $this->escape_buffer != '\\u' ) {
					throw new StateException( 'Expected "\\u" following a Unicode high surrogate. Got: ' . $this->escape_buffer );
				}

				$this->escape_buffer = '';
				$this->isSurrogate   = false;
			}
		}
		else {

			if( !ctype_xdigit( $c ) ) {
				throw new StateException( 'Expected hex character for escaped Unicode character. Unicode parsed: ' . implode( $this->buffer ) . ' and got: ' . $c );
			}
			array_push( $this->buffer, $c );

			if( count( $this->buffer ) === 4 ) {
				$codepoint = hexdec( implode( $this->buffer ) );

				if( $codepoint >= 0xD800 && $codepoint < 0xDC00 ) {
					$this->high_surrogate = $codepoint;
					$this->buffer         = [ ];
					$this->isSurrogate    = true;
				}
				elseif( $codepoint >= 0xDC00 && $codepoint <= 0xDFFF ) {
					if( $this->high_surrogate === -1 ) {
						throw new StateException( 'Missing high surrogate for Unicode low surrogate.' );
					}

					$this->finish( ( ( $this->high_surrogate - 0xD800 ) * 0x400 ) + ( $codepoint - 0xDC00 ) + 0x10000 );
				}
				else {
					if( $this->high_surrogate != -1 ) {
						throw new StateException( 'Invalid low surrogate following Unicode high surrogate.' );
					}
					else {
						$this->finish( $codepoint );
					}
				}
			}
		}
	}

	private function finish( $codepoint ) {
		$this->value = $this->convert_codepoint_to_character( $codepoint );
		$this->done  = true;
	}

	// Thanks: http://stackoverflow.com/questions/1805802/php-convert-unicode-codepoint-to-utf-8
	private function convert_codepoint_to_character( $num ) {
		if( $num <= 0x7F ) {
			return chr( $num );
		}
		if( $num <= 0x7FF ) {
			return chr( ( $num >> 6 ) + 192 ) . chr( ( $num & 63 ) + 128 );
		}
		if( $num <= 0xFFFF ) {
			return chr( ( $num >> 12 ) + 224 ) . chr( ( ( $num >> 6 ) & 63 ) + 128 ) . chr( ( $num & 63 ) + 128 );
		}
		if( $num <= 0x1FFFFF ) {
			return chr( ( $num >> 18 ) + 240 ) . chr( ( ( $num >> 12 ) & 63 ) + 128 ) . chr( ( ( $num >> 6 ) & 63 ) + 128 ) . chr( ( $num & 63 ) + 128 );
		}

		return '';
	}
}
