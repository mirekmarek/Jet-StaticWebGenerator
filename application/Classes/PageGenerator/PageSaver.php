<?php
namespace JetApplication;


use Jet\MVC_Router_Interface;


interface PageGenerator_PageSaver {
	public function save( string $URL, MVC_Router_Interface $router, string $html ) : void;
}