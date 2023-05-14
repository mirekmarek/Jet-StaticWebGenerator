<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\IO_File;
use Jet\MVC_Router_Interface;
use Jet\SysConf_Jet_Debug;
use Jet\SysConf_Jet_MVC;
use Jet\SysConf_Jet_PackageCreator_CSS;
use Jet\SysConf_Jet_PackageCreator_JavaScript;
use Jet\SysConf_Path;

require 'init/init.php';



SysConf_Jet_PackageCreator_CSS::setEnabled( true );
SysConf_Jet_PackageCreator_JavaScript::setEnabled( true );
SysConf_Jet_MVC::setCacheEnabled( false );
SysConf_Jet_Debug::setDevelMode( false );


$saver = new class() implements PageGenerator_PageSaver {
	public function save( string $URL, MVC_Router_Interface $router, string $html ): void
	{
		
		$page = $router->getPage();
		$base = $router->getBase();
		
		$path = rawurldecode( $page->getURLPath() . $router->getUrlPath() );
		
		$file_path = SysConf_Path::getData() . 'generated_html/' . $base->getId() . '/' . $page->getLocale() . $path;
		
		echo $base->getId() . ': ' . $URL . PHP_EOL;
		
		if(!str_ends_with($file_path, '.html')) {
			$file_path = rtrim($file_path, '/').'/index.html';
		}
		
		echo "\t\t".$file_path . PHP_EOL;
		echo PHP_EOL;
		
		
		IO_File::write( $file_path, $html );
	}
};

$base = Application_Web::getBase();

$generator = new PageGenerator( $base, $saver);

$generator->generate();
