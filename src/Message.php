<?php
/**
 * User: Ahmed Dabak
 * Date: 16.03.2018
 * Time: 17:30
 */

namespace Ideenkonzept\Imap;


use Carbon\Carbon;

class Message {

	private $stream;
	private $uuid;

	public function __construct( $stream, $uuid ) {
		$this->uuid   = $uuid;
		$this->stream = $stream;
	}

	public function subject() {
		return $this->decode( $this->header()->subject );
	}

	private function decode( $string ) {
		$decoded_array = imap_mime_header_decode( $string );
		$text          = '';
		for ( $i = 0; $i < count( $decoded_array ); $i ++ ) {

			switch ( strtoupper( $decoded_array[ $i ]->charset ) ) { //convert charset to uppercase
				case 'UTF-8':
					$text .= $decoded_array[ $i ]->text; //utf8 is ok
					break;
				case 'DEFAULT':
					$text .= $decoded_array[ $i ]->text; //no convert
					break;
				default:
					if ( in_array( strtoupper( $decoded_array[ $i ]->charset ), $this->upperListEncode() ) ) //found in mb_list_encodings()
					{
						$text .= mb_convert_encoding( $decoded_array[ $i ]->text, 'UTF-8', $decoded_array[ $i ]->charset );
					} else { //try to convert with iconv()
						$ret = iconv( $decoded_array[ $i ]->charset, "UTF-8", $decoded_array[ $i ]->text );
						if ( ! $ret ) {
							$text .= $decoded_array[ $i ]->text;
						}  //an error occurs (unknown charset)
						else {
							$text .= $ret;
						}
					}
					break;
			}
		}

		return $text;
	}

	public function upperListEncode() {
		$encodes = mb_list_encodings();
		foreach ( $encodes as $encode ) {
			$tencode[] = strtoupper( $encode );
		}

		return $tencode;
	}

	public function header() {
		return imap_rfc822_parse_headers( imap_fetchheader( $this->stream, $this->uuid ) );
	}

	public function senderAddress() {
		return $this->header()->senderaddress;
	}

	public function replyToAddress() {
		return $this->header()->reply_toaddress;
	}

	public function toAddress() {
		return $this->header()->toaddress;
	}

	public function fromAddress() {
		return $this->header()->fromaddress;
	}

	public function body() {
		return imap_body( $this->stream, $this->uuid );
	}

	public function date() {
		return Carbon::parse( $this->header()->date );
	}

}