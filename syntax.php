<?php
/*
 * Copyright (c) 2014 Mark C. Prins <mprins@users.sf.net>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
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
	 * Define the syntax pattern.
	 * The syntax for this plugin is: {{mapillary>imagehash&width&sequences&legs}}
	 * where imagehash is the hash of the first image of a sequence
	 * and width is the widget width in pixels. One (optional) space
	 * either after the opening pair of brackets or before the closing
	 * pair signifies floating aligment to right of left.
	 *
	 * @see http://www.mapillary.com/integrate.html
	 *
	 * @see Doku_Parser_Mode::connectTo()
	 */
	public function connectTo($mode) {
		$this->Lexer->addSpecialPattern ( '\{\{\s?mapillary>[^}\s]*\s?\}\}', $mode, 'plugin_mapillary' );
	}

	/**
	 * parse the syntax.
	 *
	 * @see DokuWiki_Syntax_Plugin::handle()
	 */
	public function handle($match, $state, $pos, Doku_Handler &$handler) {
		// check for float assignment left/right
		$float = 'none';
		$space = stripos ( $match, ' ' );
		if ($space > 0) {
			if ($space < 12) {
				$float = 'right';
			}
			if ($space > 12) {
				$float = 'left';
			}
		}
		// parse te match
		$match = substr ( str_replace ( ' ', '', $match ), 12, - 2 );
		$params = explode ( '&', $match );
		list ( $img, $width, $sequences, $legs ) = $params;
		//TODO add support for showImage=
		//TODO add support for showPlayControls=
		//TODO add support for showMap=
		//TODO add support for directions=
		//TODO add support for showThumbs=

		// make sure we have a min. width & sanity check
		$width = intval ( $width );
		if ($width < 100)
			$width = 320;
		if ($width > 2048)
			$width = 2048;

		return array (
				hsc ( $img ),
				$width,
				hsc ( $sequences ),
				hsc ( $legs ),
				$float
		);
	}

	/**
	 * Render the syntax for xhtml, odt or metadata.
	 *
	 * @see DokuWiki_Syntax_Plugin::render()
	 *
	 * @see http://www.mapillary.com/javascripts/mapillary.js
	 * @see https://dga406zepc8gy.cloudfront.net/javascripts/mapillary.js
	 */
	public function render($mode, Doku_Renderer &$renderer, $data) {
		if ($data === false)
			return false;

		static $id = 0;
		list ( $image, $width, $sequences, $legs, $float ) = $data;
		// this url might break, no idea if this url will be persistant but it
		// is mentioned in the api docs
		$image_url = 'http://d1cuyjsrcm0gby.cloudfront.net/' . $image . '/thumb-1024.jpg';

		if ($mode == 'xhtml') {
			// based on the embed javascript at http://www.mapillary.com/integrate.html
			$height = ($width / 4 * 3 * 2 - 30);
			$url = '//www.mapillary.com/jsapi/?';
			if (! empty ( $image )) {
				$url .= 'image=' . $image . '&';
			}
			if (! empty ( $sequences )) {
				$url .= 'sequences=' . $sequences . '&';
			}
			if (! empty ( $legs )) {
				$url .= 'legs=' . $legs;
			}

			$renderer->doc .= '<div id="mapillary-' . $id . '" class="mapillary-' . $float . '">';
			$renderer->doc .= '<iframe src="' . $url . '" id="mapillary-iframe-' . $id . '"';
			$renderer->doc .= '  width="' . $width . '" height="' . $height . '" title="Mapillary (' . $image . ')">';
			$renderer->doc .= '</iframe>';
			$renderer->doc .= '<figure class="mapillary-print">';
			$renderer->externalmedia ( $image_url, 'Mapillary (' . $image . ')', 'left', 1024, null, 'cache', 'nolink' );
			$renderer->doc .= '<figcaption>Mapillary: ';
			$renderer->externallink ( 'http://www.mapillary.com/map/im/' . $image, $image );
			$renderer->doc .= ' (CC BY-SA 4.0)</figcaption></figure>';
			$renderer->doc .= '</div>';
			$id ++;
			return true;
		} elseif ($mode == 'metadata') {
			global $ID;
			$rel = p_get_metadata ( $ID, 'relation', METADATA_RENDER_USING_CACHE );
			$img = $rel ['firstimage'];
			if (empty ( $img )) {
				$renderer->externalmedia ( $image_url, 'Mapillary (' . $image . ')' );
			}
			return true;
		} elseif ($mode == 'odt') {
			$renderer->p_open ();
			$renderer->externalmedia ( $image_url, 'Mapillary (' . $image . ')', 'left', 1024, null, 'cache', 'nolink' );
			$renderer->linebreak();
			$renderer->cdata('Mapillary: ');
			$renderer->externallink ( 'http://www.mapillary.com/map/im/' . $image, $image );
			$renderer->cdata(' (CC BY-SA 4.0)');
			$renderer->p_close ();
			return true;
		}
		return false;
	}
}
