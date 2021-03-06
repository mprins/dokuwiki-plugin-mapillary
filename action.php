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
if (!defined('DOKU_INC'))
	die ();

if (!defined('DOKU_PLUGIN'))
	define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN . 'action.php');

/**
 * DokuWiki Plugin mapillary (Action Component).
 *
 * @license BSD license
 * @author Mark C. Prins <mprins@users.sf.net>
 */
class action_plugin_mapillary extends DokuWiki_Action_Plugin {
	
	/**
	 * Register for events.
	 *
	 * @param Doku_Event_Handler $controller
	 *        	DokuWiki's event controller object. Also available as global $EVENT_HANDLER
	 */
	public function register(Doku_Event_Handler $controller) {
		$controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insert_mapillary_btn', array());
	}
	
	/**
	 * Inserts the toolbar button.
	 *
	 * @param Doku_Event $event
	 *        	the DokuWiki event
	 */
	function insert_mapillary_btn(Doku_Event&$event, $param) {
		$event->data [ ] = array(
				'type' => 'format',
				'title' => 'Mapillary widget',
				'icon' => '../../plugins/mapillary/images/mapillary.png',
				'open' => '{{mapillary>',
				'sample' => 'JEyWkAPk0cqzCvh09HRABg',
				'close' => '&500}}' 
		);
	}
}