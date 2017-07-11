<?php

require_once 'sys.php';

class JSObfuscator
{	
	public static $nodeJSObf = '../nodejs/js-obfuscator.js'; // path to nodejs obfuscator script
	public static $cacheFolder = '/path/to/cache/'; // cache directory
	
	public static function clearCache($mask = '*', $exception = '^$') // mask isnt regex
	{
		foreach(glob(self::$cacheFolder.$mask) as $tcache)
		if(!preg_match('@'.$exception.'@', $tcache))
		unlink($tcache);
	}

	public static function getFile($filepath)
	{
		$cachepath = self::$cacheFolder.md5($filepath).'.'.md5_file($filepath).'.'.basename($filepath); // cache file path
		$script = null;

		// clear old cache versions of $filepath
		self::clearCache(md5($filepath).'.*.'.basename($filepath), preg_quote(basename($cachepath), '@').'$');

		if(file_exists($filepath))
		{
			$script = file_get_contents($filepath);
			$obf_args = '-f '.$filepath;

			$obfuscate = null;
			$use_cache = null;
			$create_cache = null;
			$update_cache = null;

			if(preg_match('@/[/\*]!jsobf\s+([^\r\n]+)@i', $script, $match))
			{
				$match[1] = preg_replace('@(false|off|no)@i', '0', $match[1]);
				$match[1] = preg_replace('@(true|on|yes)@i', '1', $match[1]);

				if(preg_match('@\-obf[\s=]+([01])@i', $match[1], $match_obf))
				{
					switch($match_obf[1])
					{
						case '0':
							$obfuscate = false;
							break;
						case '1':
							$obfuscate = true;
							break;
					}
				}

				if(preg_match('@\-cache[\s=]+([01]|clear|(re)?set|update)@i', strtolower($match[1]), $match_cache))
				{
					switch($match_cache[1])
					{
						case 'reset':
						case 'update':
							$update_cache = true;
							$use_cache = true;
						case 'clear':
							if(file_exists($cachepath))
							unlink($cachepath);
						case '0':
							$use_cache = $use_cache != null ? $use_cache : false;
							break;
						case 'set':
						case '1':
							$create_cache = true;
							$use_cache = true;
							break;
					}
				}
			}

			if($obfuscate)
			{
				$obf_script = null;

				if(is_dir(self::$cacheFolder))
				if(($create_cache && !file_exists($cachepath)) || ($update_cache && file_exists($cachepath)))
				{
					$obf_args .= ' -dci 1'; // enable dead code injection
					$obf_script = sys::exec('nodejs '.self::$nodeJSObf.' '.$obf_args);

					file_put_contents($cachepath, $obf_script);
				}

				if($use_cache && file_exists($cachepath))
				$script = file_get_contents($cachepath);
				else
				{
					if(!$obf_script)
					$obf_script = sys::exec('nodejs '.self::$nodeJSObf.' '.$obf_args);

					$script = $obf_script;
				}

			}
		}

		return $script;
	}
}

?>
