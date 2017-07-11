<?php

class sys
{
	public static function exec($cmd)
	{
		exec($cmd, $output);
		return implode(chr(10), array_values($output));
	}
}

?>