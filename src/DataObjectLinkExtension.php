<?php
namespace FLxLabs\DataObjectLink;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Convert;
use SilverStripe\View\Requirements;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

/* Logging */
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

class DataObjectLinkExtension extends Extension {
	public function updateClientConfig(&$clientConfig)
	{
        $clientConfig['form']['editorDataObjectLink'] = [
			'schemaUrl' => $this->getOwner()->Link('methodSchema/Modals/editorDataObjectLink')
		];
	}

	public static function link_shortcode_handler($arguments, $content = null, $parser = null)
	{
        if (!isset($arguments['id']) || !is_numeric($arguments['id']) || !isset($arguments['clazz'])) {
			return null;
		}

        /** Somewhere within the Javascript code the namespace path gets lost My\New\ClassName ends as MyNewClassName
         * I was not able to find the root cause within silverstripe-dataobject-links/client/src/TinyMCE_sslink-dataobject.jsx
         *
         * See an working silverstripe example on https://github.com/silverstripe/silverstripe-cms/blob/4/client/src/legacy/TinyMCE_sslink-internal.js
         * So we need to use this dirty work arround to fix it again
         */
        $class = substr(preg_replace('/([A-Z])+/', '\\\\$1', $arguments['clazz']),1);

        Injector::inst()->get(LoggerInterface::class)->debug('DataObjectLinkExtension - link_shortcode_handler() class = ' . $class);

		if (!($obj = DataObject::get_by_id($class, $arguments['id']))
			&& !($obj = Versioned::get_latest_version($arguments['clazz'], $arguments['id']))
		) {
			return null; // There were no suitable matches at all.
		}

		$link = Convert::raw2att($obj->Link());

		if ($content) {
			return sprintf('<a href="%s">%s</a>', $link, $parser->parse($content));
		} else {
			return $link;
		}
	}
}
