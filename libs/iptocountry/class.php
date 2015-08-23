<?php

class IpToCountry {

public $IP;

private function connect() {
$ctx = stream_context_create(array('http' =>
    array(
        'timeout' => 4
    )
));
if( $data = @file_get_contents( 'http://ddweb.eu/iptocountry/check.php?ip=' . $this->IP, false, $ctx ) ) {
    return json_decode( $data );
}
    return false;
}

public function infos() {
  if( !( $data = $this->connect() ) ) {
    return (object) array( 'found' => false, 'country' => '', 'country_full' => '', 'flag' => '' );
  }
    return $data;
}

}