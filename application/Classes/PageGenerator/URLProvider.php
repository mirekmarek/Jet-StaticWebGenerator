<?php
namespace JetApplication;

use Jet\MVC_Page_Interface;


interface PageGenerator_URLProvider {
	public function getPageURLs( MVC_Page_Interface $page ) : array;
}