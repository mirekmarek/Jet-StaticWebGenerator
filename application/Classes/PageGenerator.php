<?php
namespace JetApplication;

use Jet\Http_Request;
use Jet\MVC;
use Jet\MVC_Base_Interface;
use Jet\MVC_Page_Interface;


class PageGenerator {
	
	protected MVC_Base_Interface $base;
	protected PageGenerator_PageSaver $saver;
	
	public function __construct( MVC_Base_Interface $base, PageGenerator_PageSaver $saver ) {
		$this->base = $base;
		$this->saver = $saver;
	}
	
	public function generate() : void
	{
		foreach($this->base->getLocales() as $locale ) {
			$this->generatePage(
				$this->base->getHomepage( $locale )
			);
		}
	}
	
	public function generatePage( MVC_Page_Interface $page ) : void
	{
		$page_URL = $page->getURL();
		
		$URLs = [ $page->getURL() ];
		
		foreach($page->getContent() as $content) {
			
			if(
				($module_instance = $content->getModuleInstance()) &&
				($module_instance instanceof PageGenerator_URLProvider )
			) {
				foreach($module_instance->getPageURLs($page) as $path ) {
					$URLs[] = $page_URL.$path;
				}
			}
			
		}
		
		foreach($URLs as $URL) {
			$this->generateURL( $URL );
		}
		
		
		foreach($page->getChildren() as $sub_page) {
			$this->generatePage( $sub_page );
		}
	}
	
	public function setupHttpRequest( string $URL ) : void
	{
		$parsed_URL = parse_url( $URL );
		
		$_SERVER['HTTP_HOST'] = $parsed_URL['host'];
		$_SERVER['SERVER_PORT'] = 80;
		$_SERVER['REQUEST_URI'] = $parsed_URL['path'];
		$_POST = [];
		$_GET = [];
		Http_Request::initialize();
	}
	
	public function generateURL( string $URL ) : void
	{
		$this->setupHttpRequest( $URL );
		
		$router = MVC::getRouter();
		
		$router->resolve( $URL );
		
		$base = $router->getBase();
		$locale = $router->getLocale();
		$page = $router->getPage();
		
		
		if(
			!$base->getIsActive() ||
			!$base->getLocalizedData( $locale )->getIsActive() ||
			!$page->getIsActive() ||
			$page->getIsSecret()
		) {
			return;
		}
		
		
		$html = $page->render();
		
		$this->saver->save( $URL, $router, $html );
	}
}
