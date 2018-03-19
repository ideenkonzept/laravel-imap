<?php
/**
 * User: Ahmed Dabak
 * Date: 16.03.2018
 * Time: 15:44
 */

namespace Ideenkonzept\Imap;

use Illuminate\Support\Collection;

class Client {

	private $stream;
	private $mailbox;
	private $query;

	public function __construct( $server, $username, $password, $port = "993", $flags = "/imap/ssl", $folder = 'INBOX' ) {
		$this->mailbox = sprintf( "{%s:%s%s}%s", $server, $port, $flags, $folder );
		$this->stream  = imap_open( $this->mailbox, $username, $password );
	}

	public function get() {
		if ( $query = $this->buildQuery() ) {

			return tap( new Collection(), function ( $collection ) use ($query) {
				foreach ( imap_search( $this->stream, $query ) as $uuid ) {
					$collection->push( new Message( $this->stream, $uuid ) );
				}
			} );
		}

		//else get numbers return them

//		dd( imap_check( $this->stream ) );
	}

	private function buildQuery() {
		return implode( ' ', $this->query );
	}

	public function unseen() {
		$this->query[] = "Unseen";

		return $this;
	}

	public function from( $email ) {
		$this->query[] = "FROM '{$email}'";

		return $this;
	}

	public function find( $uuid ) {
		return new Message( $this->stream, $uuid );
	}

	public function to($email) {

	}
}