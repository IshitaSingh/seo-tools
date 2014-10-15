<?php

	// +----------------------------------------------------------------------+
	// | PHP version 4.3.8                                                    |
	// +----------------------------------------------------------------------+
	// | Desc:  Html parsing Functions                                        |
	// |                                                                      |
	// +----------------------------------------------------------------------+
	// | Version: 1.0.3                                                       |
	// | Modified: Mar 07, 2006                                               |
	// +----------------------------------------------------------------------+
	//
	// $Id$


	/**
	 * remove_tag
	 * 
	 * return html tag
	 *
	 * @return      string
	 */
	function remove_tag( $str, $tag ) {
		return preg_replace( "^<$tag(.*?)>(.+?)</$tag>^si", "", $str );
	}


	/**
	 * rewrite_relative_urls
	 * 
	 * rewrite all relative urls
	 *
	 * @return      string
	 */
	function rewrite_relative_urls($html, $base) {
/*
$html .= '

<a href = "http://commerx.com" >asa</a>
<a href = "http://commerx.com/site/about_us.html" >about us</a>
<a href = "https://commerx.com/site/about_us.html" >about us</a>

<a class="test" href = "http://google.com/site" >about us</a>

<a href = "/root/somedir" arrt="2" attr2=\'3\' >asa</a>

<a href = \'/root/somedir\' 
>asa2</a>
';
 */

	    // generate server-only replacement for root-relative URLs
	    $server = preg_replace('@^([^\:]*)://([^/*]*)(/|$).*@', '\1://\2/', $base);

	    // replace root-relative URLs
	    $html = preg_replace('@\<([^>]*) (href|src)="/([^"]*)"@i', '<\1 \2="' . $server . '\3"', $html);

	    // replace base-relative URLs (kludgy, but I couldn't get ! to work)
	    $html = preg_replace('@\<([^>]*) (href|src)="(([^\:"])*|([^"]*:[^/"].*))"@i', '<\1 \2="' . $base . '\3"', $html);
/*
		$pattern = "/<a(.*?)(href)( *)=( *)[\\\"'](http|https)?[^:](.*?)[\\\"'](.*?)>/si";
		$pattern = '#(href|src)="([^:"]*)("|(?:(?:%20|\s|\+)[^"]*"))#';
		preg_match_all( $pattern, $html, $matches );
		dmp( $matches );

		$html = preg_replace( $pattern
							, '<a//1 href="//8"></a>'
							, $html );
*/
	    return $html;
	}


	/**
	 * strip_tags_attributes
	 * 
	 * strip tags and attributes
	 *
	 * @return      string
	 */
	function strip_tags_attributes($sSource, $aAllowedTags = array(), $aDisabledAttributes = array('style','bgcolor','onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavaible', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragdrop', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterupdate', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmoveout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload')) {

	    if( empty( $aDisabledAttributes )) {
	    	return strip_tags( $sSource, implode( '', $aAllowedTags ));
	    }

	    return preg_replace( '/<(.*?)>/ie', "'<' . preg_replace(array('/javascript:[^\"\']*/i', '/(" . implode('|', $aDisabledAttributes) . ")[ \\t\\n]*=[ \\t\\n]*[\"\'][^\"\']*[\"\']/i', '/\s+/'), array('', '', ' '), stripslashes('\\1')) . '>'", strip_tags( $sSource, implode( '', $aAllowedTags )));
	}


	/**
	 * parse_content
	 *
	 * parse content
	 *
	 * @return	string
	 * @access	protected
	 */
	function parse_content( $html, $url, $preserve_tags = true ) {

		// extract body
		preg_match( '^<body(.*?)>(.*?)</body>^si', $html, $matches );
		if( isset( $matches[2] )) {
			$body = $matches[2];

			// TODO: fix when no leading /
			// replace relative URL's
			$body = rewrite_relative_urls( $body, get_base_url( $url ));

			// replace img tags with src as text
			if( $preserve_tags ) {
				$body = preg_replace( "^<img(.*?)src( *)=( *)[\\\"'](.*?)[\\\"'](.*?)>^si", '[\\4]', $body );
			}

			// rem unwanted content
			$body = remove_tag( $body, 'script' );
			$body = remove_tag( $body, 'noscript' );
			$body = remove_tag( $body, 'style' );
			$body = remove_tag( $body, 'object' );

			// rem tags and attributes
			if( $preserve_tags ) {
				$body = strip_tags_attributes( $body, array( '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<h7>', '<a>', '<p>', '<br>', '<ul>', '<li>' ));
			}
			else {
				$body = strip_tags_attributes( $body );
			}

			return $body;
		}
		return '';
	}


	/**
	 * get_meta_content
	 *
	 * return meta content
	 *
	 * @return	string
	 * @access	protected
	 */
	function get_meta_content( $html, $name ) {
			// Get the 'content' attribute value in a <meta name="description" ... />
			$matches = array();

			// Search for <meta name="description" content="Buy my stuff" />
			preg_match('/<meta.*?name=("|\')'.$name.'("|\').*?content=("|\')(.*?)("|\')/i', $html, $matches);
			if (count($matches) > 4) {
				return trim($matches[4]);
			}

			// Order of attributes could be swapped around: <meta content="Buy my stuff" name="description" />
			preg_match('/<meta.*?content=("|\')(.*?)("|\').*?name=("|\')'.$name.'("|\')/i', $html, $matches);
			if (count($matches) > 2) {
				return trim($matches[2]);
			}

			// Old style with http-equiv??
			preg_match( "^<meta(.*?)(name|http-equiv)( *)=( *)[\\\"']{$name}[\\\"'](.*?)content( *)=( *)[\\\"'](.*?)[\\\"'](.*?)>^i", $html, $matches );
			if( isset( $matches[8] )) {
				return trim($matches[8]);
			}

			// No match
			return '';
	}


	/**
	 * get_base_url
	 * 
	 * return base URL
	 *
	 * @return      string
	 */
	function get_base_url( $url ) {
		$info = parse_url( $url );

		if( isset( $info['path'] )) {
			$base = substr( $info['path'], 0, strrpos( $info['path'], '/' )) . '/';
		}
		else {
			$base = '/';
		}

		if( isset( $info['port'] )) {
			return $info['scheme'] . '://' . $info['host'] . ':' . $info['port'] . $base;
		}
		else {
			return $info['scheme'] . '://' . $info['host'] . $base;
		}
	}
?>