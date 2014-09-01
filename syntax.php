<?php
/*
 * Copyright (c) 2014 Mark C. Prins <mprins@users.sf.net> Permission to use, copy, modify, and distribute this software for any purpose with or without fee is hereby granted, provided that the above copyright notice and this permission notice appear in all copies. THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */
if (! defined ( 'DOKU_INC' ))
	die ();

if (! defined ( 'DOKU_PLUGIN' ))
	define ( 'DOKU_PLUGIN', DOKU_INC . 'lib/plugins/' );

require_once (DOKU_PLUGIN . 'syntax.php');
/**
 * DokuWiki Plugin mapillary (Syntax Component).
 *
 * Handles the rendering part of the Mapillary plugin.
 *
 * @license BSD license
 * @author Mark C. Prins <mprins@users.sf.net>
 */
class syntax_plugin_mapillary extends DokuWiki_Syntax_Plugin {
	/**
	 *
	 * @see DokuWiki_Syntax_Plugin::getType()
	 */
	public function getType() {
		return 'substition';
	}

	/**
	 *
	 * @see DokuWiki_Syntax_Plugin::getPType()
	 */
	public function getPType() {
		return 'block';
	}

	/**
	 *
	 * @see Doku_Parser_Mode::getSort()
	 */
	public function getSort() {
		return 305;
	}

	/**
	 * Define the syntax.
	 * The syntax for this plugin is: {{mapillary>imagehash&width}}
	 * where imagehash is the hash of the first image of a sequence
	 * and width is the widgets width in pixels.
	 *
	 * @see http://www.mapillary.com/integrate.html
	 *
	 * @see Doku_Parser_Mode::connectTo()
	 */
	public function connectTo($mode) {
		$this->Lexer->addSpecialPattern ( '\{\{mapillary>[^}]*\}\}', $mode, 'plugin_mapillary' );
	}

	/**
	 * parse the syntax.
	 *
	 * @see DokuWiki_Syntax_Plugin::handle()
	 */
	public function handle($match, $state, $pos, Doku_Handler &$handler) {
		$match = trim ( substr ( $match, 12, - 2 ) );
		$params = explode ( '&', $match );
		$img = $params [0];
		$width = $params [1];
		if ($width < 100) {
			// make sure we have a min. width
			$width = 350;
		}
		return array (
				$img,
				$width
		);
	}

	/**
	 *
	 * @see DokuWiki_Syntax_Plugin::render()
	 */
	public function render($mode, Doku_Renderer &$renderer, $data) {
		if ($data === false) {
			return false;
		}
		list ( $img, $width ) = $data;

		if ($mode == 'xhtml') {
			// add the widget's html and script
			$renderer->doc .= '<div id="mapillary"></div>';
			$renderer->doc .= '<script src="//dga406zepc8gy.cloudfront.net/javascripts/mapillary.js" type="text/javascript" charset="utf-8"></script>';
			$renderer->doc .= '<script type="text/javascript">/*<![CDATA[*/';
			$renderer->doc .= 'Mapillary.init("mapillary", {image: "' . $img . '", width: "' . $width . '"});';
			$renderer->doc .= '/*!]]>*/</script>';
			return true;
		} elseif ($mode == 'metadata') {
			// for now return false
			return false;
		} elseif ($mode == 'odt') {
			// for now return false, in future show first image of sequence or something
			return false;
		}
		return false;
	}
}
