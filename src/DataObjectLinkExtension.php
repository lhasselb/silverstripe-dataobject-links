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
        //Injector::inst()->get(LoggerInterface::class)->debug('DataObjectLinkExtension - updateClientConfig()');
        $clientConfig['form']['editorDataObjectLink'] = [
			'schemaUrl' => $this->getOwner()->Link('methodSchema/Modals/editorDataObjectLink')
		];
        foreach ($clientConfig['form']['editorDataObjectLink'] as $key => $value) {
            //Injector::inst()->get(LoggerInterface::class)->debug('DataObjectLinkExtension - updateClientConfig() key = ' . $key. ' value = ' . $value);
        }
	}

	public static function link_shortcode_handler($arguments, $content = null, $parser = null)
	{
        Injector::inst()->get(LoggerInterface::class)->debug('DataObjectLinkExtension - link_shortcode_handler()');

        if (!isset($arguments['id']) || !is_numeric($arguments['id']) || !isset($arguments['clazz'])) {
			return null;
		}

        $class = str_replace('/', '\\', $arguments['clazz']);
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
