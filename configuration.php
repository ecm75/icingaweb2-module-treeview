<?php
namespace Icinga\Module\Treeview {

	use Icinga\Application\Version;

	/** @var \Icinga\Application\Modules\Module $this */

	$this->menuSection('Treeview')
		->setIcon('sitemap')
		->setUrl('treeview/hosts');

}
