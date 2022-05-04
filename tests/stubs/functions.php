<?php

class Snapshot_Test_Functions {
	private static $args = array();
	private static $invocations = array();

	public static function record( $function, $args ) {
		self::$args[ $function ] = $args;
		self::$invocations[ $function ] = empty( self::$invocations[ $function ] )
			? 1
			: self::$invocations[ $function ] + 1;
	}

	public static function clear() {
		self::$args = array();
		self::$invocations = array();
	}

	public static function args( $function ) {
		return empty( self::$args[ $function ] )
			? null
			: self::$args[ $function ];
	}

	public static function invocations( $function ) {
		return empty( self::$invocations[ $function ] )
			? null
			: self::$invocations[ $function ];
	}
}

function wp_mail() {
	Snapshot_Test_Functions::record( __FUNCTION__, func_get_args() );
}
