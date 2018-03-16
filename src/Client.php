<?php
/**
 * User: Ahmed Dabak
 * Date: 16.03.2018
 * Time: 15:44
 */

namespace Ideenkonzept\Imap;

class Client {

	private $stream;
	private $server;

	public function __construct( $server, $port, $username, $password, $folder = 'INBOX' ) {
		$this->server = sprintf( "{%s:%s}%s", $server, $port, $folder );
		$this->stream = imap_open( $this->serverSpecification, $username, $password );
	}


	public function folders( $folder = '*' ) {
		return imap_list( $this->stream, $this->server, $folder );
	}
}