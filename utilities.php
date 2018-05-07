<?php
// PHP Utilities
// Copyright (C) 2013-2015 Bernhard Waldbrunner
/*
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$CONST = parse_ini_file(dirname(__FILE__).'/constants.ini', true);

$PRETTYTIME = null;

function prettyTime ($t, $m = null, $format1 = "H:i", $format2 = "%02d:%02d")
{
	global $PRETTYTIME;
	if (count($PRETTYTIME))
		extract($PRETTYTIME, EXTR_SKIP);

	if (is_array($t))
		return sprintf($format2, $t['hours'], $t['minutes']);
	if (is_numeric($m))
		return sprintf($format2, $t, $m);
	return date($format1, $t);
}

$PRETTYDATE = null;

function prettyDate ($d, $m = null, $format1 = "d.m.", $format2 = "%02d.%02d.")
{
	global $PRETTYDATE;

	if (!$d)
		return '';
	if (count($PRETTYDATE))
		extract($PRETTYDATE, EXTR_SKIP);
	if (is_array($d))
		return sprintf($format2, $d['day'], $d['month']);
	if ($m)
		return sprintf($format2, $d, $m);
	return date($format1, (is_numeric($d) ? intval($d) : strtotime($d)));
}

function prettyDate_defaults ($format1 = "d.m.", $format2 = "%02d.%02d.")
{
	global $PRETTYDATE;
	$PRETTYDATE = array();
	if (istrue($format1))
		$PRETTYDATE['format1'] = $format1;
	if (istrue($format2))
		$PRETTYDATE['format2'] = $format2;
}

function prettyTime_defaults ($format1 = "H:i", $format2 = "%02d:%02d")
{
	global $PRETTYTIME;
	$PRETTYTIME = array();
	if (istrue($format1))
		$PRETTYTIME['format1'] = $format1;
	if (istrue($format2))
		$PRETTYTIME['format2'] = $format2;
}

$DATETIME = null;

function datetime ($s = true, $format = true, $separator = ' - ')
{
	global $DATETIME;
	if (count($DATETIME))
		extract($DATETIME, EXTR_SKIP);

	if ($format === true)
		$format = 'd. m. Y, H:i:s';
	elseif ($format === false)
		$format = 'd. m. Y';
	if (!$s)
		return "";
	if ($s === true)
		$s = time();
	if (is_array($s))
	{
		array_walk($s, create_function('&$v', 'if (!is_int($v)) $v = strtotime($v);'));
		return implode($separator, array_map(create_function('$v',
					   'return datetime($v, false);'), array_unique($s)));
	}
	return date($format, (is_int($s) ? $s : strtotime($s)));
}

function datetime_defaults ($format = 'd. m. Y', $separator = ' - ')
{
	global $DATETIME;
	$DATETIME = array();
	if (istrue($format))
		$DATETIME['format'] = $format;
	if (istrue($separator))
		$DATETIME['separator'] = $separator;
}

function num ($v)
{
	return is_numeric($v) ? $v : strtr($v, ',', '.');
}

function lnum ($v)
{
	return is_numeric($v) ? number_format($v, 2, ',', '') : $v;
}

function match ($regex, $subject, $m = 0)
{
	preg_match($regex, $subject, $matches);
	return isset($matches[$m]) ? $matches[$m] : false;
}

function toBool ($val)
{
	return ($val ? 'true' : 'false');
}

function istrue ($val)
{
	return $val !== null && $val !== false;
}

function is_datetime ($val)
{
	if ($val instanceof DateTime)
		return true;
	if (is_int($val))
		return null;
	$time = strtotime($val);
	return $time !== false && $time != -1;
}

function toBytes ($val)
{
	if (empty($val))
		return 0;
	$val = trim($val);
	preg_match('/([0-9]+)[\s]*([a-z]+)/i', $val, $matches);
	$last = '';
	if(isset($matches[2]))
		$last = $matches[2];
	if(isset($matches[1]))
		$val = intval($matches[1]);
	switch (strtolower($last))
	{
		case 'g':
		case 'gb':
			$val *= 1024;
		case 'm':
		case 'mb':
			$val *= 1024;
		case 'k':
		case 'kb':
			$val *= 1024;
	}
	return intval($val);
}

function swap (&$a, &$b)
{
	if ($a === $b)
		return;
	$c = $a;
	$a = $b;
	$b = $c;
}

function get ($val, $key = 0, $idx = null, $skip = true)
{
	if (is_array($val))
	{
		if (is_array($key))
		{
			$str = "";
			foreach ($key as $i => $k)
				if (isset($val[$k]) or !$skip)
					$str .= @$val[$k] . (is_array($idx) ? @$idx[$i] :
										 strval($idx));
			return $str;
		}
		elseif (isset($val[$key]))
		{
			if (is_null($idx) or is_bool($idx))
				return $val[$key];
			else
				return (is_array($val[$key]) && isset($val[$key][$idx]) ?
						$val[$key][$idx] : null);
		}
		else
			return null;
	}
	return $val;
}

function absoluteURL ($rel, $base = null)
{
	if (preg_match('/^(?:www\.)?[\w.-]+\.(?:ac|ad|aero|ae|af|ag|ai|al|am|an|ao|aq|arpa|ar|asia|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|biz|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|cat|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|coop|com|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|info|int|in|io|iq|ir|is|it|je|jm|jobs|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mobi|mo|mp|mq|mr|ms|mt|museum|mu|mv|mw|mx|my|mz|name|na|nc|net|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pro|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tel|tf|tg|th|tj|tk|tl|tm|tn|to|tp|travel|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|xn--[0-9a-z]+|ye|yt|yu|za|zm|zw)/i', $rel))
		return "http://" . $rel;
	if (parse_url($rel, PHP_URL_SCHEME))
		return $rel;
	if (!$base)
		$base = ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' .
				 $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	if ($rel[0] == '#' or $rel[0] == '?')
		return $base.$rel;

	extract(parse_url($base));

	$abs = ($rel[0] == '/' ? '' : preg_replace('#/[^/]*$#', '', $path))."/$rel";
	$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');

	for ($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n));
	return $scheme.'://'.$host.str_replace('../', '', $abs);
}

function redirect ($url, $query = "")
{
	$url = absoluteURL($url);
	if ($query)
	{
		if (strpos($url, "?") === false)
			$url .= "?";
		$first = preg_match('/\?$/', $url);
		$url .= ($first ? "" : "&") . (is_array($query) ?
				 http_build_query($query) : $query);
	}
	header("Location: ".$url);
	exit;
}

function mysql_fetch_pairs ($query, $db = null)
{
	$res = ($db ? mysql_query($query, $db) : mysql_query($query));
	$a = array();
	if ($res)
	{
		while ($r = mysql_fetch_row($res))
		{
			if (count($r) > 1)
				$a[$r[0]] = $r[1];
			else
				$a[] = @$r[0];
		}
		mysql_free_result($res);
	}
	return $a;
}

function mysql_fetch_arrays ($query, $db = null)
{
	$res = ($db ? mysql_query($query, $db) : mysql_query($query));
	$a = array();
	if ($res)
	{
		while ($r = mysql_fetch_assoc($res))
			$a[] = $r;
		mysql_free_result($res);
	}
	return $a;
}

function esc ($str)
{
	return addslashes($str);
}

function escape ($str)
{
	return mysql_real_escape_string($str);
}

function url ($str)
{
	return rawurlencode($str);
}

function htm ($str)
{
	return htmlspecialchars($str);
}

function dayOfWeek ($val, $to_string = null)
{
	global $CONST;
	if (($number = is_numeric($val)) and is_string($val))
		$val = intval($val);
	if (is_bool($to_string))
		return (($to_string && $number) || (!$to_string && !$number) ?
				 dayOfWeek($val) : $val);
	if (isset($CONST['weekdays'][$val]))
		return $CONST['weekdays'][$val];
	$weekdays = array_flip(array_map("strtoupper", $CONST['weekdays']));
	$v = strtoupper($val);
	if (isset($weekdays[$v]))
		return $weekdays[$v];
	return is_string($val) ? 0 : "";
}

function constants ($key = "", $index = null)
{
	global $CONST;
	return ($key ? ($index === null ? @$CONST[$key] : @$CONST[$key][$index]) :
			$CONST);
}

function removeDir ($dir, $rmdir = true, $rmfile = true, $countDir = false)
{
	$count = 0;
	if (is_array($rmdir))
		$rmdir = "/^(" . implode("|", $rmdir) . ')$/i';
	elseif ($rmdir === "" or $rmdir === 0)
		$rmdir = '/^$/';
	if (is_array($rmfile))
		$rmfile = "/^(" . implode("|", $rmfile) . ')$/i';
	elseif ($rmfile === "" or $rmfile === 0)
		$rmfile = '/^$/';

	foreach (glob($dir.'/*') as $file)
	{
		if (is_dir($file) and (!is_string($rmdir) or preg_match($rmdir, $file)))
			$count += removeDir($file, ($rmdir === null ? true : $rmdir),
							   ($rmfile === null ? true : $rmfile), $countDir);
		elseif ((is_string($rmfile) and preg_match($rmfile, $file)) or $rmfile)
		{
			unlink($file);
			$count++;
		}
	}
	if ((!is_string($rmfile) or $rmfile) and ((is_string($rmdir) and
		 preg_match($rmdir, $dir)) or $rmdir))
	{
		rmdir($dir);
		if ($countDir)
			$count++;
	}
	return $count;
}

function array_remove (&$input, $val, $one = false)
{
	if (!is_array($input))
		return false;
	if (!is_array($val))
		$val = array($val);

	$count = 0;
	foreach (array_keys($input) as $k)
		if (in_array($input[$k], $val))
		{
			unset($input[$k]);
			if ($one)
				return 1;
			$count++;
		}
	return $count;
}

function array_count ($input, $cond = 1000, $item_cond = null,
					  $count_array = false)
{
	if (!is_array($input))
		return null;
	$count = 0;
	$depth = is_numeric($cond);
	if ($depth and $cond < 0)
		return null;
	foreach ($input as $v)
	{
		if (is_array($v) and ($depth ? $cond : eval($cond)))
			$count += array_count($v, ($depth ? $cond-1 : $cond), $item_cond,
								  $count_array);
		elseif (!$item_cond or eval($item_cond))
			$count++;
	}
	return $count + $count_array;
}

function array_sort ($input, $func = 'sort', $option = null)
{
	if (!is_array($input))
		return false;
	$reverse = $option === true || $option < 0;
	if (is_bool($func))
	{
		if (is_string($option))
		{
			if (!$func)
				$reverse = true;
			$func = 'usort';
		}
		else
			$func = ($func ? '' : 'r').'sort';
	}
	elseif (is_numeric($func))
	{
		if ($func < 0)
			$reverse = true;
		elseif ($func == 0)
		{
			$input = array_values($input);
			return ($reverse ? array_reverse($input) : $input);
		}

		$option = null;
		$func = 'natsort';
	}
	elseif (strpos(($func = strtolower($func)), 'sort') === false)
		$func .= 'sort';

	if ($option === null or $option === false)
		$func($input);
	elseif (is_string($option))
	{
		if (preg_match('/^[\w.:!|\/\\, -]+$/', $option))
			$option = "return strcasecmp(\$a['$option'], \$b['$option']);";
		$func($input, create_function('$a, $b', $option));
	}
	else
		$func($input, $option);

	return ($reverse ? array_reverse($input, true) : $input);
}

function array_add (&$input1, $input2, $callback = null, $val = 0)
{
	if (empty($input2))
		return;
	if (!is_callable($callback)) {
		$callback = function($a, $b) {
			return $a + $b;
		};
	}
	$input1 += array_fill_keys(array_keys(array_diff_key($input2, $input1)), $val);
	array_walk($input1, function (&$a, $k, $input) {
		$b = @$input[$k];
		if (is_array($b))
			$a = (empty($a) ? $b : array_merge((array)$a, $b));
		elseif (is_array($a))
			$a[] = $b;
		elseif (array_key_exists($k, $input))
			$a = $callback($a, $b, $k);
	}, $input2);
}

function array_flatten($input, $callback = null, $prefix = '') {
    $output = array();
    if (!is_callable($callback)) {
    	$callback = function($a, $b) {
    		return ((string)$a === '' ? $b : $a . "[$b]");
    	};
    }
    foreach ($input as $key => $val) {
        $key = $callback($prefix, $key);
        if (is_array($val)) {
            $output = array_merge($output, array_flatten($val, $callback, $key));
        }
        else {
            $output[$key] = $val;
        }
    }
    return $output;
}

function truncate ($str, $len, $replace = ' ...')
{
	if (!($len and is_string($str)))
		return false;

	$str = strip_tags($str);
	if (strlen($str) > $len)
		return substr_replace($str, $replace,
							  strrpos(substr($str, 0, $len), ' '));
	return $str;
}

$SPRINTQ = null;

function sprintq ($num = 0, $word = "item", $verbs = array('is', 'are'), $plural = "s",
				  $format = '%1$d %2$s%4$s %3$s ')
{
	global $SPRINTQ;
	if (count($SPRINTQ))
		extract($SPRINTQ, EXTR_SKIP);

	$num = intval($num);
	$n = abs($num);
	if (is_array($word))
	{
		$plural = '';
		if (count($word) == 1)
			$word = @$word[0];
		else
		{
			if (count($word) == 2)
				array_unshift($word, @$word[1]);
			$word = (isset($word[$n]) ? $word[$n] : end($word));
		}
	}
	elseif (!is_array($plural))
		$plural = ($n == 1 ? "" : $plural);
	elseif (count($plural) == 1)
		$plural = ($n == 1 ? "" : @$plural[0]);
	else
	{
		if (count($plural) == 2)
			array_unshift($plural, @$plural[1]);
		$plural = (isset($plural[$n]) ? $plural[$n] : end($plural));
	}
	if (!is_array($verbs))
		$verb = $verbs;
	elseif (count($verbs) == 1)
		$verb = @$verbs[0];
	else
	{
		if (count($verbs) == 2)
			array_unshift($verbs, @$verbs[1]);
		$verb = (isset($verbs[$n]) ? $verbs[$n] : end($verbs));
	}
	return sprintf($format, $num, $word, $verb, $plural);
}

function printq_defaults ($word = null, $verbs = null, $plural = null, $format = null)
{
	global $SPRINTQ;
	$SPRINTQ = array();
	if (istrue($word))
		$SPRINTQ['word'] = $word;
	if (istrue($verbs))
		$SPRINTQ['verbs'] = $verbs;
	if (istrue($plural))
		$SPRINTQ['plural'] = $plural;
	if (istrue($format))
		$SPRINTQ['format'] = $format;
}

function printq ($num = 0, $word = "item", $verbs = array('is', 'are'), $plural = "s",
				 $format = '%1$d %2$s%4$s %3$s')
{
	print sprintq($num, $word, $verbs, $plural, $format);
}

function metricFormat ($val, $unit = "B", $base = 1000, $precision = 2, $point = '.',
	$thousand = '', $format = '%1$s %2$s')
{
	static $SI = array(
		24  => "Y",
		21  => "Z",
		18  => "E",
		15  => "P",
		12  => "T",
		9   => "G",
		6   => "M",
		3   => "k",
		2   => "h",
		1   => "da",
		-1  => "d",
		-2  => "c",
		-3  => "m",
		-6  => "µ",
		-9  => "n",
		-12 => "p",
		-15 => "f",
		-18 => "a",
		-21 => "z",
		-24 => "y"
	);
	foreach ($SI as $e => $prefix)
	{
		$v = (pow(100, $e) * $val) / pow($base, $e);
		if (abs($v) > 0.9 and abs($v) < $base+0.1)
		{
			$val = $v;
			$unit = $prefix.$unit;
			break;
		}
	}
    $rem = abs($precision) - strlen(intval($val)) + 1;
    return sprintf($format, number_format($val,
        ($precision >= 0 ? $precision : ($rem > 0 ? $rem : 0)), $point, $thousand), $unit);
}

function websafe ($file)
{
	return preg_replace('/[\s!*();:@&=+$,\'"\/?#\[\]_{}<>]+/', '_',
		   preg_replace('/[^\s\x20-\x7E]/', '', iconv("utf-8", "ascii//TRANSLIT", $file)));
}

function valid_email ($address)
{
	$domain = substr(strrchr($address, "@"), 1);
	return filter_var($address, FILTER_VALIDATE_EMAIL) && checkdnsrr($domain, 'MX');
}

$MVUPLOADEDFILE = null;

function mv_uploaded_file ($orig, $dest, $separator = '-', $websafe = true, $format = '%s%s%s.%s')
{
	global $MVUPLOADEDFILE;

	if (count($MVUPLOADEDFILE))
		extract($MVUPLOADEDFILE, EXTR_SKIP);

	if ($dest === '')
		$dest = $orig;
	$file = pathinfo($dest);
	if ($websafe)
		$file['basename'] = websafe($file['basename']);
	$dir = dirname($dest);
	$i = 0;
	do
	{
		$name = sprintf($format, $file['basename'], ($i ? $separator : ''), ($i ? $i : ''), $file['extension']);
		$i++;
	}
	while (file_exists("$dir/$name"));

	if (move_uploaded_file($orig, "$dir/$name"))
		return $name;
	return null;
}

function mv_uploaded_file_defaults ($separator = '-', $websafe = true, $format = '%s%s%s.%s')
{
	global $MVUPLOADEDFILE;

	$MVUPLOADEDFILE = array();
	if (istrue($format))
		$MVUPLOADEDFILE['format'] = $format;
	if (istrue($separator))
		$MVUPLOADEDFILE['separator'] = $separator;
	if (istrue($websafe))
		$MVUPLOADEDFILE['websafe'] = $websafe;
}

function versionval ($v)
{
	if (!$v)
		return "0";
	$v = explode(".", $v);
	return sprintf("%03d%03d%04d%04d", $v[0], @$v[1], @$v[2], @$v[3]);
}

function csv_parse ($str, $headers = false, $trim = false, $delim = ',', $quote = '"')
{
    $str = preg_replace_callback("/([^$quote]*)($quote(($quote$quote|[^$quote])*)$quote|\$)/s", function ($matches) use ($delim, $quote) {
	    $str = str_replace("\r", "\rR", @$matches[3]);
	    $str = str_replace("\n", "\rN", $str);
	    $str = str_replace($quote.$quote, "\rQ", $str);
	    $str = str_replace($delim, "\rC", $str);
	    return preg_replace('/\r\n?/', "\n", $matches[1]) . $str;
	}, $str);
    $str = preg_replace('/\n$/', '', $str);

    $csv = array_map(function ($line) use ($trim, $delim, $quote) {
	    return array_map(function ($field) use ($trim, $delim, $quote) {
			if ($trim)
				$field = trim($field);
		    $field = str_replace("\rC", $delim, $field);
		    $field = str_replace("\rQ", $quote, $field);
		    $field = str_replace("\rN", "\n", $field);
		    $field = str_replace("\rR", "\r", $field);
		    return $field;
		}, explode($delim, $line));
	}, explode("\n", $str));

	if (is_null($headers))
		array_shift($csv);
	elseif ($headers)
	{
		$headers = array_shift($csv);
		foreach (array_keys($csv) as $c)
		{
			$row =& $csv[$c];
			$row = array_combine($headers, $row);
		}
	}

	return $csv;
}

function csv_serialize ($data, $headers = false, $trim = false, $delim = ',', $quote = '"', $escape = '"')
{
	if (!is_array(reset($data)))
		$data = array($data);
	if ($headers)
		array_unshift($data, array_keys(current($data)));

	return implode("\n", array_map(function ($row) use ($trim, $delim, $quote, $escape) {
		return implode($delim, array_map(function ($val) use ($trim, $delim, $quote, $escape) {
			$q = ($quote ? $quote : $delim);
			if ($trim)
				$val = trim($val);
			if ($escape != $quote)
				$val = str_replace($escape, $escape.$escape, $val);
			if (!$quote)
				$val = str_replace("\n", "$escape\n", $val);
			return ($val === '' ? '' : $quote.str_replace($q, $escape.$q, $val).$quote);
		}, $row));
	}, $data));
}
?>
